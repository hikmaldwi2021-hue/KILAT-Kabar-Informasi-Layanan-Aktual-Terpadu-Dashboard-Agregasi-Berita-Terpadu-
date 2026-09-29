<?php

namespace App\Console\Commands;

use App\Models\Berita;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class PerbaikiFormatBerita extends Command
{
    protected $signature = 'berita:perbaiki-format {--dry-run : Hanya mengecek tanpa mengubah database}';

    protected $description = 'Memperbaiki format isi berita lama tanpa mengubah ringkasan dan kategori';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        $beritaList = Berita::with('opd')
            ->whereNotNull('source_id')
            ->whereNotNull('opd_id')
            ->get();

        if ($beritaList->isEmpty()) {
            $this->info('Tidak ada berita yang perlu diperbaiki.');

            return self::SUCCESS;
        }

        $this->info("Ditemukan {$beritaList->count()} berita.");

        if ($dryRun) {
            $this->warn('MODE DRY-RUN: Database TIDAK akan diubah.');
        }

        $this->newLine();

        $berhasil = 0;
        $tidakBerubah = 0;
        $tidakDitemukan = 0;
        $gagal = 0;

        foreach ($beritaList as $berita) {
            $this->line("Memproses: {$berita->judul}");

            try {
                if (! $berita->opd || ! $berita->opd->endpoint_api) {
                    $this->warn('  Endpoint API OPD tidak tersedia.');
                    $gagal++;
                    continue;
                }

                $response = Http::timeout(30)
                    ->connectTimeout(20)
                    ->retry(2, 3000)
                    ->get($berita->opd->endpoint_api);

                if ($response->failed()) {
                    $this->error(
                        "  Gagal API: HTTP {$response->status()}"
                    );

                    $gagal++;
                    continue;
                }

                $items = $response->json('daftarkonten') ?? [];

                $item = collect($items)->first(function ($item) use ($berita) {
                    return (string) ($item['Nid'] ?? '') === (string) $berita->source_id;
                });

                if (! $item) {
                    $this->warn(
                        "  Source ID {$berita->source_id} tidak ditemukan."
                    );

                    $tidakDitemukan++;
                    continue;
                }

                $isiBaru = $this->bersihkanHtml(
                    $item['body'] ?? ''
                );

                if ($isiBaru === '') {
                    $this->warn('  Isi dari API kosong.');
                    $gagal++;
                    continue;
                }

                // Cek apakah isi memang berubah.
                if ($berita->isi === $isiBaru) {
                    $this->comment('  - Tidak ada perubahan.');
                    $tidakBerubah++;
                    continue;
                }

                // =====================================================
                // DRY RUN
                // =====================================================
                // Pada mode ini TIDAK ADA update database.
                if ($dryRun) {
                    $this->info('  ✓ Akan diperbaiki (DRY-RUN).');
                    $berhasil++;
                    continue;
                }

                // =====================================================
                // UPDATE DATABASE
                // =====================================================
                // HANYA kolom "isi" yang diubah.
                $berita->update([
                    'isi' => $isiBaru,
                ]);

                $this->info('  ✓ Berhasil diperbaiki.');

                $berhasil++;

            } catch (\Throwable $e) {
                $this->error(
                    '  Error: ' . $e->getMessage()
                );

                $gagal++;
            }
        }

        $this->newLine();
        $this->info('=== SELESAI ===');

        if ($dryRun) {
            $this->info("Akan diperbaiki : {$berhasil}");
        } else {
            $this->info("Berhasil        : {$berhasil}");
        }

        $this->info("Tidak berubah   : {$tidakBerubah}");
        $this->info("Tidak ditemukan : {$tidakDitemukan}");
        $this->info("Gagal           : {$gagal}");

        if ($dryRun) {
            $this->newLine();
            $this->warn('DRY-RUN selesai. Tidak ada data yang diubah.');
        }

        return self::SUCCESS;
    }

    protected function bersihkanHtml(string $html): string
    {
        // Ubah elemen HTML tertentu menjadi line break.
        $html = preg_replace(
            [
                '/<br\s*\/?>/i',
                '/<\/p\s*>/i',
                '/<\/div\s*>/i',
                '/<\/li\s*>/i',
            ],
            "\n",
            $html
        );

        // Hapus tag HTML lainnya.
        $text = strip_tags($html);

        // Decode HTML entity.
        $text = html_entity_decode(
            $text,
            ENT_QUOTES,
            'UTF-8'
        );

        // Ubah non-breaking space menjadi spasi biasa.
        $text = str_replace(
            "\xc2\xa0",
            ' ',
            $text
        );

        // Normalisasi line ending Windows/Mac/Linux.
        $text = str_replace(
            ["\r\n", "\r"],
            "\n",
            $text
        );

        // Rapikan spasi dan tab.
        // Newline tetap dipertahankan.
        $text = preg_replace(
            '/[ \t]+/',
            ' ',
            $text
        );

        // Hapus spasi di awal dan akhir setiap baris.
        $text = preg_replace(
            '/^[ \t]+|[ \t]+$/m',
            '',
            $text
        );

        // Maksimal satu baris kosong antar paragraf.
        $text = preg_replace(
            "/\n{3,}/",
            "\n\n",
            $text
        );

        return trim($text);
    }
}