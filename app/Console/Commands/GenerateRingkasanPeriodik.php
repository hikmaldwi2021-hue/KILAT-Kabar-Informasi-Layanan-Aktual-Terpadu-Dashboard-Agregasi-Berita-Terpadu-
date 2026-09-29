<?php

namespace App\Console\Commands;

use App\Models\LogProses;
use App\Services\RingkasanPeriodikService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateRingkasanPeriodik extends Command
{
    protected $signature = 'ringkasan:generate {tipe=mingguan}';
    protected $description = 'Generate ringkasan periodik (harian/mingguan/bulanan) dari berita terbaru';

    public function handle(RingkasanPeriodikService $service): void
    {
        $tipe = $this->argument('tipe');

        [$mulai, $akhir] = match ($tipe) {
            'harian' => [Carbon::now()->subDay(), Carbon::now()],
            'mingguan' => [Carbon::now()->subWeek(), Carbon::now()],
            'bulanan' => [Carbon::now()->subMonth(), Carbon::now()],
            default => [Carbon::now()->subWeek(), Carbon::now()],
        };

        $this->info("Membuat ringkasan {$tipe} untuk periode {$mulai->toDateString()} - {$akhir->toDateString()}...");

        try {
            $ringkasan = $service->generate($tipe, $mulai, $akhir);

            $this->info("Selesai. Ringkasan ID: {$ringkasan->id}");
            $this->line($ringkasan->narasi);

            LogProses::create([
                'nama_proses' => "generate_ringkasan_{$tipe}",
                'status' => 'sukses',
                'jumlah_diproses' => $ringkasan->data_agregat['total_berita'] ?? 0,
                'keterangan' => "Ringkasan ID {$ringkasan->id} berhasil dibuat.",
                'dijalankan_pada' => now(),
            ]);
        } catch (\Throwable $e) {
            $this->error("Gagal: {$e->getMessage()}");

            LogProses::create([
                'nama_proses' => "generate_ringkasan_{$tipe}",
                'status' => 'gagal',
                'jumlah_diproses' => 0,
                'keterangan' => $e->getMessage(),
                'dijalankan_pada' => now(),
            ]);
        }
    }
}