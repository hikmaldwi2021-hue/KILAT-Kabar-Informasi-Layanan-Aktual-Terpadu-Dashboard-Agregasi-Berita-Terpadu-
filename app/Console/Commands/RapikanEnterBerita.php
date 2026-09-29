<?php

namespace App\Console\Commands;

use App\Models\Berita;
use Illuminate\Console\Command;

class RapikanEnterBerita extends Command
{
    protected $signature = 'berita:rapikan-enter
                            {--dry-run : Hanya mengecek tanpa mengubah database}';

    protected $description = 'Merapikan Enter ganda pada isi berita tanpa mengubah field lain';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        $beritaList = Berita::query()
            ->whereNotNull('isi')
            ->get();

        if ($beritaList->isEmpty()) {
            $this->info('Tidak ada berita yang ditemukan.');
            return self::SUCCESS;
        }

        $this->info("Total berita: {$beritaList->count()}");

        if ($dryRun) {
            $this->warn('MODE DRY-RUN: Database TIDAK akan diubah.');
        }

        $this->newLine();

        $akanDiperbaiki = 0;
        $tidakBerubah = 0;

        foreach ($beritaList as $berita) {
            $isiLama = $berita->isi;

            // Hanya ubah Enter ganda atau lebih menjadi satu Enter.
            $isiBaru = preg_replace("/\n+/", "\n", $isiLama);

            if ($isiLama === $isiBaru) {
                $tidakBerubah++;
                continue;
            }

            $jumlahEnterLama = substr_count($isiLama, "\n");
            $jumlahEnterBaru = substr_count($isiBaru, "\n");

            $this->line("✓ {$berita->judul}");
            $this->line(
                "  Enter: {$jumlahEnterLama} → {$jumlahEnterBaru}"
            );

            $akanDiperbaiki++;

            if (! $dryRun) {
                // HANYA field isi yang diubah.
                $berita->update([
                    'isi' => $isiBaru,
                ]);
            }
        }

        $this->newLine();
        $this->info('================================');
        $this->info('HASIL');
        $this->info('================================');

        if ($dryRun) {
            $this->info("Akan diperbaiki : {$akanDiperbaiki}");
        } else {
            $this->info("Berhasil        : {$akanDiperbaiki}");
        }

        $this->info("Tidak berubah   : {$tidakBerubah}");

        if ($dryRun) {
            $this->newLine();
            $this->warn('DRY-RUN selesai.');
            $this->warn('Tidak ada data yang diubah.');
        } else {
            $this->newLine();
            $this->info('Selesai. Hanya field "isi" yang diubah.');
        }

        return self::SUCCESS;
    }
}