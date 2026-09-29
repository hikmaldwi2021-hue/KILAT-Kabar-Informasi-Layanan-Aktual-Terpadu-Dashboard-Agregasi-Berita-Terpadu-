<?php

namespace App\Services;

use App\Ai\Agents\RingkasanPeriodikGenerator;
use App\Models\Berita;
use App\Models\RingkasanPeriodik;
use Carbon\Carbon;

class RingkasanPeriodikService
{
    public function generate(string $tipe, Carbon $mulai, Carbon $akhir): RingkasanPeriodik
    {
        $dataAgregat = $this->kumpulkanDataAgregat($mulai, $akhir);

        $narasi = $this->generateNarasi($tipe, $dataAgregat);

        return RingkasanPeriodik::create([
            'tipe' => $tipe,
            'periode_mulai' => $mulai->toDateString(),
            'periode_akhir' => $akhir->toDateString(),
            'narasi' => $narasi,
            'data_agregat' => $dataAgregat,
        ]);
    }

    protected function kumpulkanDataAgregat(Carbon $mulai, Carbon $akhir): array
    {
        $berita = Berita::with(['opd', 'kategori'])
            ->whereBetween('tanggal_publish', [$mulai->toDateString(), $akhir->toDateString()])
            ->get();

        $totalBerita = $berita->count();

        $perOpd = $berita->groupBy('opd.nama')
            ->map(fn ($group) => $group->count())
            ->sortDesc();

        $perKategori = $berita->groupBy('kategori.nama')
            ->map(fn ($group) => $group->count())
            ->sortDesc();

        $kategoriTerbanyak = $this->cekKategoriTerbanyak($perKategori);

        return [
            'total_berita' => $totalBerita,
            'opd_paling_aktif' => $perOpd->keys()->first(),
            'jumlah_opd_aktif' => $perOpd->count(),
            'per_opd' => $perOpd->toArray(),
            'kategori_terbanyak' => $kategoriTerbanyak['nama'],
            'kategori_seri' => $kategoriTerbanyak['seri'],
            'jumlah_kategori' => $perKategori->count(),
            'per_kategori' => $perKategori->toArray(),
            'ringkasan_berita' => $berita->take(10)->map(fn ($b) => [
                'judul' => $b->judul,
                'ringkasan' => $b->ringkasan,
            ])->toArray(),
        ];
    }

    protected function cekKategoriTerbanyak($perKategori): array
{
    if ($perKategori->isEmpty()) {
        return ['nama' => null, 'seri' => []];
    }

    $nilaiTertinggi = $perKategori->first();
    $yangSeri = $perKategori->filter(fn ($jumlah) => $jumlah === $nilaiTertinggi)->keys();

    return [
        'nama' => $yangSeri->implode(' dan '),
        'seri' => $yangSeri->toArray(),
    ];
}

    protected function generateNarasi(string $tipe, array $dataAgregat): string
{
    if ($dataAgregat['total_berita'] === 0) {
        return "Tidak ada berita baru pada minggu lalu.";
    }

    $labelPeriode = match ($tipe) {
        'mingguan' => 'minggu lalu',
        default => 'periode ini',
    };

    $ringkasanList = collect($dataAgregat['ringkasan_berita'])
        ->map(fn ($item) => "{$item['judul']}: {$item['ringkasan']}")
        ->implode('; ');

    $prompt = "Buatkan pemberitahuan singkat untuk masyarakat tentang aktivitas pemberitaan {$labelPeriode}.\n"
        . "Gunakan frasa '{$labelPeriode}' di awal kalimat.\n"
        . "- Total berita: {$dataAgregat['total_berita']}\n"
        . "- Jumlah OPD aktif: {$dataAgregat['jumlah_opd_aktif']}\n"
        . "- OPD paling aktif: {$dataAgregat['opd_paling_aktif']}\n"
        . "- Kategori terbanyak: {$dataAgregat['kategori_terbanyak']}\n"
        . "- Rincian per OPD: " . json_encode($dataAgregat['per_opd']) . "\n"
        . "- Ringkasan beberapa berita: {$ringkasanList}";

    try {
        /** @var \Laravel\Ai\Responses\StructuredAgentResponse $response */
        $response = (new RingkasanPeriodikGenerator)->prompt($prompt);
        return $response['narasi'];
    } catch (\Throwable $e) {
        \Illuminate\Support\Facades\Log::error('Gagal generate narasi periodik', ['error' => $e->getMessage()]);
        return "Ringkasan {$labelPeriode} sedang tidak dapat dibuat karena gangguan sementara pada layanan AI.";
    }
}
}