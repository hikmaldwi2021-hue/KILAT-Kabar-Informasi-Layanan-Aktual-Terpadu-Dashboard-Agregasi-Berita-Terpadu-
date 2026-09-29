<?php

namespace App\Console\Commands;

use App\Jobs\FetchBeritaOpd;
use App\Models\Opd;
use Illuminate\Console\Command;

class FetchAllBeritaOpd extends Command
{
    protected $signature = 'opd:fetch-all';
    protected $description = 'Tarik data berita dari semua OPD yang punya endpoint API aktif';

    public function handle(): void
    {
        $opdList = Opd::where('status_aktif', true)
            ->whereNotNull('endpoint_api')
            ->where('endpoint_api', '!=', '')
            ->get();

        if ($opdList->isEmpty()) {
            $this->warn('Tidak ada OPD dengan endpoint API aktif.');
            return;
        }

        $this->info("Ditemukan {$opdList->count()} OPD dengan endpoint API. Memproses...");

        foreach ($opdList as $opd) {
            FetchBeritaOpd::dispatch($opd);
            $this->line("→ Dispatch fetch untuk: {$opd->nama}");
        }

        $this->info('Selesai. Semua job fetch telah di-dispatch ke antrian.');
    }
}