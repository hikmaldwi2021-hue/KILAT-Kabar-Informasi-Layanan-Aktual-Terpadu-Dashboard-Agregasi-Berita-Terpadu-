<?php

namespace App\Ai\Agents;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Attributes\Model;
use Laravel\Ai\Attributes\Provider;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Enums\Lab;
use Laravel\Ai\Promptable;

#[Provider(Lab::Gemini)]
#[Model('gemini-3.6-flash')]
class RingkasanPeriodikGenerator implements Agent, HasStructuredOutput
{
    use Promptable;

    public function instructions(): string
{
    return 'Kamu adalah asisten yang membuat pemberitahuan singkat untuk masyarakat umum '
        . 'mengenai aktivitas pemberitaan pemerintahan pada periode sebelumnya. '
        . 'WAJIB mulai kalimat pertama dengan referensi waktu yang jelas dan natural, '
        . 'contoh: "Minggu lalu, ..." atau "Pekan kemarin, ...". '
        . 'Gunakan gaya bahasa pemberitahuan/pengumuman yang ramah dan mudah dipahami '
        . 'masyarakat umum, seperti mengumumkan kabar terbaru, bukan laporan analitis formal. '
        . 'Jika ada beberapa kategori dengan jumlah berita yang SAMA BANYAK (seri), '
        . 'sebutkan SEMUA kategori tersebut secara setara, jangan menyebut hanya satu '
        . 'seolah-olah dia unggul sendirian. '
        . 'Buat 2-4 kalimat saja.';
}
    public function schema(JsonSchema $schema): array
    {
        return [
            'narasi' => $schema->string()->required(),
        ];
    }
}