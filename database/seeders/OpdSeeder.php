<?php

namespace Database\Seeders;

use App\Models\Opd;
use Illuminate\Database\Seeder;

class OpdSeeder extends Seeder
{
    public function run(): void
    {
        $opds = [

            // =====================
            // DINAS
            // =====================
            ['nama' => 'Dinas Pendidikan', 'kode' => 'DISDIK'],
            ['nama' => 'Dinas Kesehatan', 'kode' => 'DINKES'],
            ['nama' => 'Dinas Pekerjaan Umum dan Penataan Ruang', 'kode' => 'PUPR'],
            ['nama' => 'Dinas Perumahan dan Kawasan Permukiman', 'kode' => 'PERKIM'],
            ['nama' => 'Dinas Sosial dan Pemberdayaan Masyarakat Desa', 'kode' => 'DINSOSPMD'],
            ['nama' => 'Dinas Tenaga Kerja', 'kode' => 'DISNAKER'],
            ['nama' => 'Dinas Pangan', 'kode' => 'DISPANGAN'],
            ['nama' => 'Dinas Lingkungan Hidup dan Kehutanan', 'kode' => 'DLHK'],
            ['nama' => 'Dinas Kependudukan dan Pencatatan Sipil', 'kode' => 'DISDUKCAPIL'],
            ['nama' => 'Dinas Pemberdayaan Perempuan, Perlindungan Anak, dan Kependudukan Keluarga Berencana', 'kode' => 'DP3AKB'],
            ['nama' => 'Dinas Perhubungan', 'kode' => 'DISHUB'],
            ['nama' => 'Dinas Komunikasi dan Informatika', 'kode' => 'DISKOMINFO'],
            ['nama' => 'Dinas Koperasi, Usaha Kecil dan Menengah', 'kode' => 'DISKOPUKM'],
            ['nama' => 'Dinas Penanaman Modal dan Pelayanan Terpadu Satu Pintu', 'kode' => 'DPMPTSP'],
            ['nama' => 'Dinas Kebudayaan dan Pariwisata', 'kode' => 'DISBUDPAR'],
            ['nama' => 'Dinas Kelautan dan Perikanan', 'kode' => 'DKP'],
            ['nama' => 'Dinas Pertanian dan Ketahanan Pangan', 'kode' => 'DPKP'],
            ['nama' => 'Dinas Energi dan Sumber Daya Mineral', 'kode' => 'ESDM'],
            ['nama' => 'Dinas Perindustrian dan Perdagangan', 'kode' => 'DISPERINDAG'],

            // =====================
            // BADAN
            // =====================
            ['nama' => 'Badan Perencanaan Pembangunan Daerah', 'kode' => 'BAPPEDA'],
            ['nama' => 'Badan Keuangan Daerah', 'kode' => 'BAKEUDA'],
            ['nama' => 'Badan Kepegawaian dan Pengembangan Sumber Daya Manusia Daerah', 'kode' => 'BKPSDMD'],
            ['nama' => 'Badan Penghubung Daerah', 'kode' => 'BPD'],
            ['nama' => 'Badan Penanggulangan Bencana Daerah', 'kode' => 'BPBD'],
            ['nama' => 'Badan Kesatuan Bangsa dan Politik', 'kode' => 'KESBANGPOL'],

            // =====================
            // LAINNYA
            // =====================
            ['nama' => 'Inspektorat Daerah', 'kode' => 'INSPEKTORAT'],
            ['nama' => 'RSUD Dr. (H.C.) Ir. Soekarno', 'kode' => 'RSUD'],
        ];

        foreach ($opds as $opd) {
            Opd::create([
                'nama' => $opd['nama'],
                'kode' => $opd['kode'],
                'endpoint_api' => null,
                'status_aktif' => true,
            ]);
        }
    }
}