<?php

namespace App\Jobs;

use App\Models\Berita;
use App\Models\Kategori;
use App\Services\GeminiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * @deprecated Sejak migrasi ke Laravel AI SDK, class ini sudah tidak digunakan
 * dalam alur produksi. Dipertahankan sebagai dokumentasi perbandingan
 * implementasi manual (HTTP client + parsing JSON custom) vs pendekatan
 * menggunakan Laravel AI SDK dengan structured output.
 *
 * Lihat: App\Ai\Agents\BeritaBatchSummarizer (pengganti untuk kebutuhan batch)
 * Lihat: App\Ai\Agents\BeritaSingleSummarizer (pengganti untuk kebutuhan single-berita)
 */

class ProcessNewsWithAI implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected Berita $berita)
    {
    }

    public function handle(GeminiService $gemini): void
{
    $daftarKategori = Kategori::pluck('nama')->toArray();

    $hasil = $gemini->ringkasDanKategorikan(
        $this->berita->judul,
        $this->berita->isi,
        $daftarKategori
    );

    if (! $hasil) {
        Log::warning('Gagal proses AI untuk berita', ['berita_id' => $this->berita->id]);
        return;
    }

    $this->berita->simpanHasilAi($hasil);
    }
}