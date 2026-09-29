<?php

namespace App\Console\Commands;

use App\Models\Berita;
use Illuminate\Console\Command;

class CekEnterBerita extends Command
{
    protected $signature = 'berita:cek-enter';

    protected $description = 'Mengecek pola Enter pada isi berita tanpa mengubah database';

    public function handle(): int
    {
        $beritaList = Berita::with('opd')
            ->whereNotNull('source_id')
            ->whereNotNull('isi')
            ->get();

        if ($beritaList->isEmpty()) {
            $this->info('Tidak ada berita.');
            return self::SUCCESS;
        }

        $this->info("Total berita: {$beritaList->count()}");
        $this->newLine();

        foreach ($beritaList->take(10) as $index => $berita) {
            $isi = $berita->isi;

            $jumlahEnter = substr_count($isi, "\n");
            $jumlahDoubleEnter = preg_match_all("/\n{2,}/", $isi);

            $this->line('========================================');
            $this->line('NO      : ' . ($index + 1));
            $this->line('JUDUL   : ' . $berita->judul);
            $this->line('OPD     : ' . ($berita->opd->nama ?? '-'));
            $this->line('SOURCE  : ' . $berita->source_id);
            $this->line('ENTER   : ' . $jumlahEnter);
            $this->line('DOUBLE+ : ' . $jumlahDoubleEnter);

            $this->line('ISI:');

            // Tampilkan Enter sebagai simbol agar terlihat jelas
            $preview = str_replace(
                ["\r", "\n"],
                ["", " ↵ "],
                $isi
            );

            $this->line($preview);
            $this->newLine();
        }

        $this->info('========================================');
        $this->info('Command ini hanya membaca data.');
        $this->info('Database TIDAK diubah.');

        return self::SUCCESS;
    }
}