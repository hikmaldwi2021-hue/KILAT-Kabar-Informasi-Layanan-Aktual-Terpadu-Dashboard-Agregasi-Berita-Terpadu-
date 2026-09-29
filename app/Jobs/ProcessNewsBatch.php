<?php

namespace App\Jobs;

use App\Ai\Agents\BeritaBatchSummarizer;
use App\Models\Berita;
use App\Models\Kategori;
use App\Models\LogProses;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class ProcessNewsBatch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 30;

    /**
     * @param Collection<int, Berita> $beritaBatch
     */
    public function __construct(protected Collection $beritaBatch)
    {
    }

    public function handle(): void
    {
        $daftarKategori = Kategori::pluck('nama')->toArray();

        $daftarBerita = $this->beritaBatch->map(function (Berita $b) {
            return "Berita (ID: {$b->id})\nJudul: {$b->judul}\nIsi: {$b->isi}";
        })->implode("\n\n---\n\n");

        try {
            $agent = new BeritaBatchSummarizer($daftarKategori);
            $response = $agent->prompt($daftarBerita);
        } catch (\Throwable $e) {
            Log::error('Gagal proses AI batch via Laravel AI SDK', [
                'error' => $e->getMessage(),
                'jumlah_berita' => $this->beritaBatch->count(),
                'percobaan_ke' => $this->attempts(),
            ]);

            $this->catatLog('gagal', 0,
                "Gagal memanggil AI (percobaan ke-{$this->attempts()}): {$e->getMessage()}",
                [
                    'error' => $e->getMessage(),
                    'berita' => $this->beritaBatch->map(fn ($b) => [
                        'id' => $b->id,
                        'judul' => $b->judul,
                        'status' => 'tidak diproses',
                    ])->toArray(),
                ]
            );

            throw $e;
        }

        $hasil = collect($response['hasil'] ?? []);
        $jumlahSukses = 0;
        $detailBerita = [];

        foreach ($this->beritaBatch as $berita) {
            $item = $hasil->firstWhere('id', $berita->id);

            if (! $item) {
                Log::warning('ID hasil AI tidak cocok dengan batch', ['berita_id' => $berita->id]);
                $detailBerita[] = ['id' => $berita->id, 'judul' => $berita->judul, 'status' => 'gagal dicocokkan'];
                continue;
            }

            $berita->simpanHasilAi($item);
            $detailBerita[] = ['id' => $berita->id, 'judul' => $berita->judul, 'status' => 'sukses'];
            $jumlahSukses++;
        }

        $this->catatLog(
            $jumlahSukses === $this->beritaBatch->count() ? 'sukses' : 'sebagian',
            $jumlahSukses,
            "{$jumlahSukses} dari {$this->beritaBatch->count()} berita berhasil diproses AI.",
            ['berita' => $detailBerita]
        );
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('ProcessNewsBatch gagal permanen setelah semua percobaan', [
            'error' => $exception->getMessage(),
            'jumlah_berita' => $this->beritaBatch->count(),
        ]);

        $this->catatLog('gagal', 0,
            "Gagal permanen setelah {$this->tries}x percobaan: {$exception->getMessage()}",
            [
                'error' => $exception->getMessage(),
                'berita' => $this->beritaBatch->map(fn ($b) => [
                    'id' => $b->id,
                    'judul' => $b->judul,
                    'status' => 'gagal permanen',
                ])->toArray(),
            ]
        );
    }

    protected function catatLog(string $status, int $jumlah, string $keterangan, array $detail = []): void
    {
        LogProses::create([
            'nama_proses' => 'proses_ai_batch',
            'status' => $status,
            'jumlah_diproses' => $jumlah,
            'keterangan' => $keterangan,
            'detail' => $detail,
            'dijalankan_pada' => now(),
        ]);
    }
}