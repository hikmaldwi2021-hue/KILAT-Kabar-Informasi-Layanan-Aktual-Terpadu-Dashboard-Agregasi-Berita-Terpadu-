<?php

namespace App\Filament\Widgets;

use App\Models\Berita;
use App\Models\RingkasanPeriodik;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RingkasanPeriodikWidget extends BaseWidget
{
    protected ?string $pollingInterval = '30s';

    protected function getColumns(): int
    {
        return 2;
    }

    protected function getStats(): array
    {
        $terbaru = RingkasanPeriodik::latest('created_at')->first();

        if (! $terbaru) {
            return [
                Stat::make('Ringkasan Terkini', 'Belum ada data')
                    ->description('Jalankan proses ringkasan periodik terlebih dahulu')
                    ->columnSpan('full'),
            ];
        }

        $data = $terbaru->data_agregat;
        $labelTipe = ucfirst($terbaru->tipe);

        // Hitung keseluruhan (all-time), langsung dari tabel berita
        $opdPalingAktifKeseluruhan = Berita::query()
            ->join('opd', 'berita.opd_id', '=', 'opd.id')
            ->selectRaw('opd.nama, count(*) as jumlah')
            ->groupBy('opd.nama')
            ->orderByDesc('jumlah')
            ->first();

        $kategoriTerbanyakKeseluruhan = Berita::query()
            ->join('kategori', 'berita.kategori_id', '=', 'kategori.id')
            ->selectRaw('kategori.nama, count(*) as jumlah')
            ->groupBy('kategori.nama')
            ->orderByDesc('jumlah')
            ->first();

        $jumlahOpdAktifKeseluruhan = Berita::query()->distinct('opd_id')->count('opd_id');
        $jumlahKategoriKeseluruhan = Berita::query()->whereNotNull('kategori_id')->distinct('kategori_id')->count('kategori_id');

        return [
            // Card narasi (full width)
            Stat::make("Ringkasan {$labelTipe} Terkini", $data['total_berita'])
                ->description($terbaru->narasi)
                ->color('success')
                ->columnSpan('full'),

            // Card keseluruhan (all-time, real-time dari database)
            Stat::make('OPD Paling Aktif (Keseluruhan)', $opdPalingAktifKeseluruhan->nama ?? '-')
                ->description(($opdPalingAktifKeseluruhan->jumlah ?? 0) . " berita telah diunggah")
                ->descriptionIcon(Heroicon::OutlinedBuildingOffice)
                ->color('info'),

            Stat::make('Kategori Terbanyak (Keseluruhan)', $kategoriTerbanyakKeseluruhan->nama ?? '-')
                ->description(($kategoriTerbanyakKeseluruhan->jumlah ?? 0) . " berita menggunakan kategori ini")
                ->descriptionIcon(Heroicon::OutlinedTag)
                ->color('info'),
        ];
    }
}