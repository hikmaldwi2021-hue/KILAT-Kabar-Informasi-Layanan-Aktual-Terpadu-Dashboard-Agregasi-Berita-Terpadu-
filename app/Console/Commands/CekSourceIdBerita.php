<?php

namespace App\Console\Commands;

use App\Models\Berita;
use App\Models\Opd;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class CekSourceIdBerita extends Command
{
    protected $signature = 'berita:cek-source-id';

    protected $description = 'Mengecek kecocokan source_id berita dengan data dari API OPD tanpa mengubah database';

    public function handle(): int
    {
        $this->info('=== CEK SOURCE ID BERITA ===');
        $this->newLine();

        $opds = Opd::whereIn(
            'id',
            Berita::whereNotNull('source_id')
                ->whereNotNull('opd_id')
                ->distinct()
                ->pluck('opd_id')
        )->get();

        if ($opds->isEmpty()) {
            $this->warn('Tidak ada OPD yang memiliki berita.');
            return self::SUCCESS;
        }

        $totalDatabase = 0;
        $totalApi = 0;
        $totalCocok = 0;
        $totalTidakDitemukan = 0;
        $totalGagal = 0;

        foreach ($opds as $opd) {
            $this->line("Memproses OPD: {$opd->nama}");

            $beritaDatabase = Berita::where('opd_id', $opd->id)
                ->whereNotNull('source_id')
                ->get();

            $jumlahDatabase = $beritaDatabase->count();

            try {
                if (! $opd->endpoint_api) {
                    $this->error('  Endpoint API tidak tersedia.');
                    $totalGagal++;
                    continue;
                }

                $response = Http::timeout(30)
                    ->connectTimeout(20)
                    ->retry(2, 3000)
                    ->get($opd->endpoint_api);

                if ($response->failed()) {
                    $this->error(
                        "  API gagal: HTTP {$response->status()}"
                    );

                    $totalGagal++;
                    continue;
                }

                $items = $response->json('daftarkonten') ?? [];

                $sourceIdsApi = collect($items)
                    ->pluck('Nid')
                    ->filter()
                    ->map(fn ($id) => (string) $id)
                    ->unique()
                    ->values();

                $sourceIdsDatabase = $beritaDatabase
                    ->pluck('source_id')
                    ->map(fn ($id) => (string) $id)
                    ->values();

                $cocok = $sourceIdsDatabase
                    ->intersect($sourceIdsApi)
                    ->count();

                $tidakDitemukan = $sourceIdsDatabase
                    ->diff($sourceIdsApi)
                    ->count();

                $jumlahApi = $sourceIdsApi->count();

                $this->info("  Database       : {$jumlahDatabase}");
                $this->info("  API            : {$jumlahApi}");
                $this->info("  Cocok          : {$cocok}");
                $this->warn("  Tidak ditemukan: {$tidakDitemukan}");

                if ($tidakDitemukan > 0) {
                    $idsHilang = $sourceIdsDatabase
                        ->diff($sourceIdsApi)
                        ->values();

                    $this->comment(
                        '  ID tidak ditemukan: ' .
                        $idsHilang->implode(', ')
                    );
                }

                $this->newLine();

                $totalDatabase += $jumlahDatabase;
                $totalApi += $jumlahApi;
                $totalCocok += $cocok;
                $totalTidakDitemukan += $tidakDitemukan;

            } catch (\Throwable $e) {
                $this->error(
                    '  Error: ' . $e->getMessage()
                );

                $totalGagal++;
                $this->newLine();
            }
        }

        $this->info('================================');
        $this->info('HASIL AKHIR');
        $this->info('================================');

        $this->info("Total database    : {$totalDatabase}");
        $this->info("Total data API    : {$totalApi}");
        $this->info("Total cocok       : {$totalCocok}");
        $this->warn("Total tidak ada   : {$totalTidakDitemukan}");
        $this->error("Total API gagal   : {$totalGagal}");

        $this->newLine();

        $this->comment(
            'Command ini hanya membaca data. Tidak ada database yang diubah.'
        );

        return self::SUCCESS;
    }
}