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
class BeritaBatchSummarizer implements Agent, HasStructuredOutput
{
    use Promptable;

    public function __construct(protected array $daftarKategori = [])
    {
    }

    public function instructions(): string
    {
        $kategoriStr = implode(', ', $this->daftarKategori);

        return "Kamu adalah asisten yang meringkas dan mengategorikan berita pemerintahan. "
            . "Proses SEMUA berita yang diberikan. Setiap berita punya ID unik yang harus "
            . "dikembalikan persis sama. Kategori harus salah satu dari: {$kategoriStr}.";
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'hasil' => $schema->array()->items(
                $schema->object(fn ($s) => [
                    'id' => $s->integer()->required(),
                    'ringkasan' => $s->string()->required(),
                    'kategori' => $s->string()->required(),
                ])
            )->required(),
        ];
    }
}