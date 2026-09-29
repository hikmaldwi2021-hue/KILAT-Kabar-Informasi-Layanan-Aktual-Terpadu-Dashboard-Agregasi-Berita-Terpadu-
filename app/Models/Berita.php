<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Berita extends Model
{
    protected $table = 'berita';

    protected $fillable = [
        'opd_id',
        'source_id',
        'judul',
        'isi',
        'ringkasan',
        'kategori_id',
        'kategori_asal_ai',
        'dikoreksi_manual',
        'dikoreksi_oleh',
        'tanggal_publish',
        'link_asal',
        'thumbnail',
        'galeri',
        'penulis',
        'fotografer',
        'editor',
        'sumber',
        'bidang_informasi',
        'kategori_info_publik',
    ];

    protected $casts = [
        'tanggal_publish' => 'date',
        'dikoreksi_manual' => 'boolean',
        'galeri' => 'array',
    ];

    public function opd()
    {
        return $this->belongsTo(Opd::class);
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    public function kategoriAsalAi()
    {
        return $this->belongsTo(
            Kategori::class,
            'kategori_asal_ai'
        );
    }

    public function dikoreksiOleh()
    {
        return $this->belongsTo(
            User::class,
            'dikoreksi_oleh'
        );
    }

    public function simpanHasilAi(?array $hasil): void
    {
        if (! $hasil) {
            return;
        }

        $kategori = Kategori::where('nama', $hasil['kategori'] ?? '')->first();

        $this->update([
            'ringkasan' => $hasil['ringkasan'] ?? null,
            'kategori_id' => $kategori?->id,
            'kategori_asal_ai' => $kategori?->id,
        ]);
    }
}