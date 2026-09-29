<?php

namespace App\Filament\Widgets;

use App\Models\Berita;
use Filament\Widgets\ChartWidget;

class BeritaPerOpdChart extends ChartWidget
{
    protected int|string|array $columnSpan = 'full';

    protected ?string $heading = 'Jumlah Berita per OPD';

    protected string $view = 'filament.widgets.berita-per-opd-chart';

    public ?string $tanggalMulai = null;

    public function mount(): void
    {
        parent::mount();

        $this->tanggalMulai = now()
            ->startOfMonth()
            ->format('Y-m-d');
    }

    public function updatedTanggalMulai(): void
    {
        // Paksa data chart dihitung ulang
        $this->cachedData = null;

        $this->updateChartData();
    }

    protected function getData(): array
    {
        $data = Berita::query()
            ->join('opd', 'berita.opd_id', '=', 'opd.id')
            ->selectRaw('opd.nama, COUNT(*) as jumlah')
            ->whereDate(
                'berita.tanggal_publish',
                '>=',
                $this->tanggalMulai ?? now()->startOfMonth()->toDateString()
            )
            ->whereDate(
                'berita.tanggal_publish',
                '<=',
                now()->toDateString()
            )
            ->groupBy('opd.nama')
            ->orderByDesc('jumlah')
            ->get();

        $maxJumlah = $data->max('jumlah') ?: 1;

        $warnaPerBar = $data->map(function ($item) use ($maxJumlah) {
            $jumlah = (int) $item->jumlah;

            if ($jumlah === 0) {
                return '#d1d5db';
            }

            $persentase = ($jumlah / $maxJumlah) * 100;
            $hue = ($persentase / 100) * 130;

            return "hsl({$hue}, 80%, 50%)";
        });

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Berita',
                    'data' => $data->pluck('jumlah')->values()->all(),
                    'backgroundColor' => $warnaPerBar->values()->all(),
                ],
            ],
            'labels' => $data->pluck('nama')->values()->all(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y',

            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],

            'scales' => [
                'x' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0,
                    ],
                ],
            ],
        ];
    }
}