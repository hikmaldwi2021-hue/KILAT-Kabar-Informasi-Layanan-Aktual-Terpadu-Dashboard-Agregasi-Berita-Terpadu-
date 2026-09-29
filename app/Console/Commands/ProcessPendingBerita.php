<?php

namespace App\Console\Commands;

use App\Jobs\ProcessNewsBatch;
use App\Models\Berita;
use App\Models\LogProses;
use Illuminate\Console\Command;

class ProcessPendingBerita extends Command
{
    protected $signature = 'berita:process-pending';
    protected $description = 'Proses berita yang belum punya ringkasan AI, kalau ada';

    public function handle(): void
    {
        $pending = Berita::whereNull('ringkasan')->get();

        if ($pending->isEmpty()) {
            $this->info('Tidak ada berita baru yang perlu diproses. Skip.');

            LogProses::create([
                'nama_proses' => 'process_pending_berita',
                'status' => 'kosong',
                'jumlah_diproses' => 0,
                'keterangan' => 'Tidak ada berita pending saat dijalankan.',
                'dijalankan_pada' => now(),
            ]);

            return;
        }

        $this->info("Ditemukan {$pending->count()} berita pending. Memproses...");

        $batches = $pending->chunk(20);

        foreach ($batches as $batch) {
            ProcessNewsBatch::dispatch($batch);
        }

        $this->info("Selesai. {$batches->count()} batch job telah di-dispatch.");

        LogProses::create([
            'nama_proses' => 'process_pending_berita',
            'status' => 'sukses',
            'jumlah_diproses' => $pending->count(),
            'keterangan' => "{$batches->count()} batch job telah di-dispatch ke antrian.",
            'dijalankan_pada' => now(),
        ]);
    }
}