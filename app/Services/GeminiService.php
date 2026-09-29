<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
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

class GeminiService
{
    protected string $apiKey;
    protected string $model;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key');
        $this->model = config('services.gemini.model');
    }

    public function ringkasDanKategorikan(string $judul, string $isi, array $daftarKategori): ?array
    {
        $daftarKategoriStr = implode(', ', $daftarKategori);

        $prompt = <<<PROMPT
        Kamu adalah asisten yang meringkas berita pemerintahan.
        Balas HANYA dalam format JSON berikut, tanpa teks lain, tanpa markdown code block:
        {"ringkasan": "ringkasan 2-3 kalimat", "kategori": "salah satu dari: {$daftarKategoriStr}"}

        Judul: {$judul}
        Isi: {$isi}
        PROMPT;

        $response = Http::post(
            "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}",
            [
                'contents' => [
                    ['parts' => [['text' => $prompt]]],
                ],
            ]
        );

        if ($response->failed()) {
            Log::error('Gemini API gagal', ['response' => $response->body()]);
            return null;
        }

        $text = $response->json('candidates.0.content.parts.0.text');

        if (! $text) {
            Log::error('Gemini API: response kosong', ['raw' => $response->json()]);
            return null;
        }

        $clean = trim(str_replace(['```json', '```'], '', $text));
        $result = json_decode($clean, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('Gemini API: gagal parse JSON', ['raw_text' => $text]);
            return null;
        }

        return $result;
    }

    public function ringkasDanKategorikanBatch(array $items, array $daftarKategori): ?array
{
    $daftarKategoriStr = implode(', ', $daftarKategori);

    $daftarBerita = collect($items)->map(function ($item) {
        return "ID: {$item['id']}\nJudul: {$item['judul']}\nIsi: {$item['isi']}";
    })->implode("\n\n---\n\n");

    $prompt = <<<PROMPT
    Kamu adalah asisten yang meringkas dan mengategorikan berita pemerintahan.
    Berikut beberapa berita, tiap berita punya ID unik. Proses SEMUA berita ini.
    Balas HANYA array JSON seperti ini, tanpa teks lain, tanpa markdown code block:
    [{"id": 1, "ringkasan": "ringkasan 2-3 kalimat", "kategori": "salah satu dari: {$daftarKategoriStr}"}, ...]

    Pastikan jumlah item dalam array JSON sama dengan jumlah berita yang diberikan, dan "id" harus persis sama dengan ID yang diberikan.

    Berita-berita:
    {$daftarBerita}
    PROMPT;

    $response = Http::timeout(60)->post(
        "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}",
        [
            'contents' => [
                ['parts' => [['text' => $prompt]]],
            ],
        ]
    );

    if ($response->failed()) {
        Log::error('Gemini API batch gagal', ['response' => $response->body()]);
        return null;
    }

    $text = $response->json('candidates.0.content.parts.0.text');

    if (! $text) {
        Log::error('Gemini API batch: response kosong', ['raw' => $response->json()]);
        return null;
    }

    $clean = trim(str_replace(['```json', '```'], '', $text));
    $result = json_decode($clean, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        Log::error('Gemini API batch: gagal parse JSON', ['raw_text' => $text]);
        return null;
    }

    return $result;
    }
}