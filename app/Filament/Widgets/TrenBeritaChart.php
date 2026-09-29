<?php

namespace App\Filament\Widgets;

use App\Models\Berita;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class TrenBeritaChart extends ChartWidget
{
    protected int|string|array $columnSpan = 'full';

    protected ?string $heading = 'Tren Jumlah Berita';

    protected function getFilters(): ?array
    {
        return [
            '14' => '14 Hari Terakhir',
            '30' => '30 Hari Terakhir',
            '90' => '3 Bulan Terakhir (mingguan)',
            '365' => '1 Tahun Terakhir (bulanan)',
        ];
    }

    protected function getData(): array
    {
        $filter = $this->filter ?? '30';

        return match ($filter) {
            '90' => $this->dataMingguan(12),
            '365' => $this->dataBulanan(12),
            default => $this->dataHarian((int) $filter),
        };
    }

    protected function dataHarian(int $jumlahHari): array
    {
        $mulai = Carbon::now()->subDays($jumlahHari - 1)->startOfDay();

        $raw = Berita::query()
            ->selectRaw('DATE(tanggal_publish) as periode, count(*) as jumlah')
            ->where('tanggal_publish', '>=', $mulai)
            ->groupBy('periode')
            ->pluck('jumlah', 'periode');

        $labels = [];
        $data = [];

        for ($i = 0; $i < $jumlahHari; $i++) {
            $tanggal = $mulai->copy()->addDays($i);
            $key = $tanggal->toDateString();

            $labels[] = $tanggal->translatedFormat('d M');
            $data[] = $raw->get($key, 0);
        }

        return $this->bentukDataset($labels, $data);
    }

    protected function dataMingguan(int $jumlahMinggu): array
    {
        $labels = [];
        $data = [];

        for ($i = $jumlahMinggu - 1; $i >= 0; $i--) {
            $mulaiMinggu = Carbon::now()->subWeeks($i)->startOfWeek();
            $akhirMinggu = $mulaiMinggu->copy()->endOfWeek();

            $jumlah = Berita::whereBetween('tanggal_publish', [$mulaiMinggu, $akhirMinggu])->count();

            $labels[] = $mulaiMinggu->translatedFormat('d M');
            $data[] = $jumlah;
        }

        return $this->bentukDataset($labels, $data);
    }

    protected function dataBulanan(int $jumlahBulan): array
    {
        $labels = [];
        $data = [];

        for ($i = $jumlahBulan - 1; $i >= 0; $i--) {
            $bulan = Carbon::now()->subMonths($i);

            $jumlah = Berita::whereYear('tanggal_publish', $bulan->year)
                ->whereMonth('tanggal_publish', $bulan->month)
                ->count();

            $labels[] = $bulan->translatedFormat('M Y');
            $data[] = $jumlah;
        }

        return $this->bentukDataset($labels, $data);
    }

    protected function bentukDataset(array $labels, array $data): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Berita',
                    'data' => $data,
                    'borderColor' => '#D4A853',
                    'backgroundColor' => 'rgba(212, 168, 83, 0.15)',
                    'fill' => true,
                    'tension' => 0.35,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}