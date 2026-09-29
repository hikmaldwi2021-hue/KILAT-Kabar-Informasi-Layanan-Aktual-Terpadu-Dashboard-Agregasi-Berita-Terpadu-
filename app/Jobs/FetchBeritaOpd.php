<?php

namespace App\Jobs;

use App\Models\Berita;
use App\Models\LogProses;
use App\Models\Opd;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchBeritaOpd implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected Opd $opd)
    {
    }

    public function handle(): void
    {
        try {
            $response = Http::timeout(30)
            ->connectTimeout(20) // naikin dari default 10 detik jadi 20 detik
            ->retry(2, 3000) // coba ulang 2x, jeda 3 detik antar percobaan
            ->get($this->opd->endpoint_api);

            if ($response->failed()) {
                $this->catatLog('gagal', 0, "HTTP error: {$response->status()}");
                return;
            }

            $items = $response->json('daftarkonten') ?? [];

            if (empty($items)) {
                $this->catatLog('kosong', 0, 'Tidak ada konten dari API.');
                return;
            }

            $jumlahBaru = 0;

            foreach ($items as $item) {
                $sourceId = (string) ($item['Nid'] ?? null);

                if (! $sourceId) {
                    continue;
                }

                $sudahAda = Berita::where('opd_id', $this->opd->id)
                    ->where('source_id', $sourceId)
                    ->exists();

                if ($sudahAda) {
                    continue;
                }

                Berita::create([
                    'opd_id' => $this->opd->id,
                    'source_id' => $sourceId,
                    'judul' => trim($item['title'] ?? '-'),
                    'isi' => $this->bersihkanHtml($item['body'] ?? ''),
                    'tanggal_publish' => $this->parseTanggal($item['created'] ?? null),
                    'link_asal' => $this->buatLinkAsal('detail-post' . ($item['slug'] ?? '')),
                    'thumbnail' => $item['thumb_foto'] ?? null,
                    'galeri' => $this->parseGaleri($item['field_images'] ?? null),
                    'penulis' => $item['field_penulis'] ?? null,
                    'fotografer' => $item['field_fotografer'] ?? null,
                    'editor' => $item['field_editor'] ?? null,
                    'sumber' => $item['field_sumber'] ?? null,
                    'bidang_informasi' => $item['field_bidang_informasi'] ?? null,
                    'kategori_info_publik' => $item['field_kategori_info_publik'] ?? null,
                ]);

                $jumlahBaru++;
            }

            $this->catatLog('sukses', $jumlahBaru, "{$jumlahBaru} berita baru ditambahkan dari {$this->opd->nama}.");

        } catch (\Throwable $e) {
            Log::error('Gagal fetch berita OPD', [
                'opd' => $this->opd->nama,
                'error' => $e->getMessage(),
            ]);
            $this->catatLog('gagal', 0, $e->getMessage());
        }
    }

    protected function bersihkanHtml(string $html): string
{
    // Ubah beberapa tag HTML yang memang menandakan baris/paragraf
    $html = preg_replace(
        [
            '/<br\s*\/?>/i',
            '/<\/p>/i',
            '/<\/div>/i',
            '/<\/li>/i',
        ],
        "\n",
        $html
    );

    // Hapus tag HTML lainnya
    $text = strip_tags($html);

    // Decode entity HTML seperti &nbsp;, &amp;, dll.
    $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');

    // Ubah non-breaking space menjadi spasi biasa
    $text = str_replace("\xc2\xa0", ' ', $text);

    // Rapikan spasi/tabs, tapi JANGAN menghapus newline
    $text = preg_replace('/[ \t]+/', ' ', $text);

    // Rapikan terlalu banyak baris kosong
    $text = preg_replace("/\n{3,}/", "\n\n", $text);

    return trim($text);
}

    protected function parseTanggal(?string $created): string
    {
        if (! $created) {
            return now()->toDateString();
        }

        try {
            return Carbon::createFromFormat('m/d/Y - H:i', trim($created))->toDateString();
        } catch (\Throwable) {
            return now()->toDateString();
        }
    }

    protected function parseGaleri(?string $fieldImages): ?array
    {
        if (! $fieldImages) {
            return null;
        }

        return collect(explode(',', $fieldImages))
            ->map(fn ($url) => trim($url))
            ->filter()
            ->values()
            ->toArray();
    }

    protected function buatLinkAsal(?string $slug): ?string
    {
        if (! $slug) {
            return null;
        }

        $domainPublik = str($this->opd->endpoint_api)
            ->replace('drupal.', '')
            ->before('/api');

        return rtrim($domainPublik, '/') . '/' . ltrim($slug, '/');
    }

    protected function catatLog(string $status, int $jumlah, string $keterangan): void
    {
        LogProses::create([
            'nama_proses' => 'fetch_berita_' . str($this->opd->nama)->slug(),
            'status' => $status,
            'jumlah_diproses' => $jumlah,
            'keterangan' => $keterangan,
            'dijalankan_pada' => now(),
        ]);
    }
}