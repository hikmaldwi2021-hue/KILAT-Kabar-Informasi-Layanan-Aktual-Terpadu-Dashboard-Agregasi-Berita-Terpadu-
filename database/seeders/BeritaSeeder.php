<?php

namespace Database\Seeders;

use App\Models\Berita;
use App\Models\Opd;
use Illuminate\Database\Seeder;

class BeritaSeeder extends Seeder
{
    public function run(): void
    {
        $opds = Opd::all();

        if ($opds->isEmpty()) {
            $this->command->warn('Belum ada data OPD. Seed Opd dulu sebelum jalanin BeritaSeeder.');
            return;
        }

        $daftarBerita = [
            [
                'judul' => 'Dinas Kesehatan Gelar Vaksinasi Massal di 12 Puskesmas',
                'isi' => 'Dinas Kesehatan menggelar program vaksinasi massal serentak di 12 puskesmas se-kabupaten mulai hari ini. Program ini menyasar sekitar 8.000 warga dengan prioritas lansia dan anak-anak. Kepala Dinas Kesehatan mengimbau masyarakat untuk membawa kartu identitas dan kartu keluarga saat mendaftar di lokasi.',
                'tanggal_publish' => now()->subDays(1),
            ],
            [
                'judul' => 'Pendaftaran PPDB Jenjang SMP Resmi Dibuka Hari Ini',
                'isi' => 'Dinas Pendidikan mengumumkan pembukaan Penerimaan Peserta Didik Baru (PPDB) jenjang SMP tahun ajaran ini secara daring melalui portal resmi. Kuota penerimaan tahun ini sebanyak 3.500 siswa yang tersebar di 25 sekolah negeri. Pendaftaran dibuka hingga akhir bulan dengan sistem zonasi, afirmasi, dan prestasi.',
                'tanggal_publish' => now()->subDays(2),
            ],
            [
                'judul' => 'Perbaikan Jalan Provinsi Sepanjang 4,2 KM Mulai Dikerjakan',
                'isi' => 'Dinas Pekerjaan Umum dan Penataan Ruang memulai proyek perbaikan jalan yang rusak akibat curah hujan tinggi beberapa bulan terakhir. Perbaikan mencakup ruas sepanjang 4,2 kilometer dan ditargetkan rampung dalam waktu 45 hari kerja. Pengendara diimbau untuk berhati-hati dan mengikuti jalur alternatif yang telah disiapkan.',
                'tanggal_publish' => now()->subDays(3),
            ],
            [
                'judul' => 'Bantuan Sosial Tahap II Mulai Disalurkan ke 15.000 KPM',
                'isi' => 'Dinas Sosial mulai menyalurkan bantuan sosial tahap kedua kepada 15.000 Keluarga Penerima Manfaat (KPM) di seluruh kecamatan. Penyaluran dilakukan melalui kantor pos dan bank penyalur yang telah ditunjuk. Proses ini ditargetkan selesai dalam dua minggu ke depan.',
                'tanggal_publish' => now()->subDays(4),
            ],
            [
                'judul' => 'Gerakan Penghijauan 5.000 Pohon Digelar di Kawasan Rawan Longsor',
                'isi' => 'Dinas Lingkungan Hidup bersama komunitas peduli lingkungan menanam 5.000 bibit pohon di kawasan yang rawan longsor. Kegiatan ini merupakan bagian dari program rehabilitasi lahan kritis yang ditargetkan mencakup 50 hektare dalam tiga tahun ke depan.',
                'tanggal_publish' => now()->subDays(5),
            ],
            [
                'judul' => 'Festival Budaya Tahunan Resmi Dibuka, Diikuti 30 Sanggar Seni',
                'isi' => 'Dinas Pariwisata dan Kebudayaan membuka gelaran festival budaya tahunan yang diikuti oleh 30 sanggar seni dari berbagai kecamatan. Acara yang berlangsung selama tiga hari ini menampilkan pertunjukan tari tradisional, pameran kerajinan lokal, dan bazar UMKM.',
                'tanggal_publish' => now()->subDays(6),
            ],
            [
                'judul' => 'Program Bantuan Bibit Unggul untuk 2.000 Petani Padi',
                'isi' => 'Dinas Pertanian menyalurkan bantuan bibit padi unggul kepada 2.000 petani di enam kecamatan sebagai bagian dari program peningkatan produktivitas pangan. Bantuan ini juga disertai pendampingan teknis dari penyuluh pertanian lapangan selama satu musim tanam.',
                'tanggal_publish' => now()->subDays(7),
            ],
            [
                'judul' => 'Uji Coba Angkutan Umum Gratis untuk Pelajar Diperpanjang',
                'isi' => 'Dinas Perhubungan memperpanjang masa uji coba program angkutan umum gratis untuk pelajar hingga akhir semester. Program ini bertujuan mengurangi kepadatan lalu lintas di jam sekolah sekaligus meringankan biaya transportasi keluarga.',
                'tanggal_publish' => now()->subDays(8),
            ],
            [
                'judul' => 'Pelatihan Digital Marketing Gratis untuk 500 Pelaku UMKM',
                'isi' => 'Dinas Koperasi, UKM, dan Perdagangan menggelar pelatihan digital marketing gratis bagi 500 pelaku UMKM guna meningkatkan penjualan melalui platform daring. Pelatihan mencakup materi fotografi produk, pengelolaan marketplace, hingga strategi promosi media sosial.',
                'tanggal_publish' => now()->subDays(9),
            ],
            [
                'judul' => 'Layanan Perekaman KTP Elektronik Keliling Masuk 8 Desa Terpencil',
                'isi' => 'Dinas Kependudukan dan Pencatatan Sipil meluncurkan layanan jemput bola perekaman KTP elektronik ke delapan desa terpencil yang sulit menjangkau kantor kecamatan. Layanan ini menyasar warga lansia dan penyandang disabilitas yang belum memiliki dokumen kependudukan.',
                'tanggal_publish' => now()->subDays(10),
            ],
        ];

        foreach ($daftarBerita as $index => $item) {
            $opd = $opds[$index % $opds->count()];

            Berita::create([
                'opd_id' => $opd->id,
                'source_id' => 'dummy-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                'judul' => $item['judul'],
                'isi' => $item['isi'],
                'tanggal_publish' => $item['tanggal_publish'],
                'link_asal' => 'https://' . str($opd->nama)->slug() . '.contohkabupaten.go.id/berita/' . str($item['judul'])->slug(),
            ]);
        }

        $this->command->info(count($daftarBerita) . ' dummy berita berhasil dibuat.');
    }
}