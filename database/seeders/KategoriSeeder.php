<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kategori;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        $kategori = [
            'Kesehatan',
            'Pendidikan',
            'Infrastruktur',
            'Ekonomi',
            'Sosial',
            'Pemerintahan',
            'Lingkungan',
            'Pariwisata',
            'Teknologi',
            'Lainnya',
        ];

        foreach ($kategori as $item) {
            Kategori::create([
                'nama' => $item,
            ]);
        }
    }
}