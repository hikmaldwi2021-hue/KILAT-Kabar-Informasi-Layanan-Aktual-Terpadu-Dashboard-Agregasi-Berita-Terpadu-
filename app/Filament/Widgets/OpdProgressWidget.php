<?php

namespace App\Filament\Widgets;

use App\Models\Berita;
use App\Models\Opd;
use Filament\Widgets\Widget;

class OpdProgressWidget extends Widget
{
    protected string $view = 'filament.widgets.opd-progress-widget';

    protected int|string|array $columnSpan = 'full';

    public function getOpdData()
    {
        $jumlahPerOpd = Berita::query()
            ->selectRaw('opd_id, count(*) as jumlah, max(tanggal_publish) as terakhir')
            ->groupBy('opd_id')
            ->get()
            ->keyBy('opd_id');

        $maxJumlah = $jumlahPerOpd->max('jumlah') ?: 1;

        return Opd::orderBy('nama')
            ->get()
            ->map(function ($opd) use ($jumlahPerOpd, $maxJumlah) {
                $info = $jumlahPerOpd->get($opd->id);
                $jumlah = $info->jumlah ?? 0;
                $terakhir = $info?->terakhir;
                $persentase = round(($jumlah / $maxJumlah) * 100);

                $statusLabel = match (true) {
                    empty($opd->endpoint_api) => 'Belum ada API',
                    ! $terakhir => 'Belum ada berita',
                    now()->diffInDays($terakhir) <= 7 => 'Aktif',
                    now()->diffInDays($terakhir) <= 30 => 'Mulai senyap',
                    default => 'Tidak aktif',
                };

                $warnaBar = ($jumlah === 0)
                    ? '#d1d5db'
                    : $this->warnaGradien($persentase);

                return [
                    'nama' => $opd->nama,
                    'jumlah' => $jumlah,
                    'persentase' => $persentase,
                    'terakhir' => $terakhir ? \Carbon\Carbon::parse($terakhir)->diffForHumans() : '-',
                    'status_label' => $statusLabel,
                    'status_warna' => $warnaBar,
                ];
            })
            ->sortByDesc('jumlah')
            ->values();
    }

    protected function warnaGradien(int $persentase): string
    {
        // 0% = merah (hue 0), 100% = hijau (hue 130)
        $hue = ($persentase / 100) * 130;

        return "hsl({$hue}, 80%, 50%)";
    }
}