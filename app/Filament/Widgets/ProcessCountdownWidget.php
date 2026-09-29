<?php

namespace App\Filament\Widgets;

use App\Models\Berita;
use App\Models\LogProses;
use App\Models\Opd;
use Carbon\Carbon;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;

class ProcessCountdownWidget extends Widget
{
    protected string $view = 'filament.widgets.process-countdown-widget';

    protected int|string|array $columnSpan = 'full';

    public function getProcessCards(): Collection
    {
        $now = now();

        $nextAiRun = $this->nextDailyTime(
            $now,
            ['08:05', '12:05', '18:05']
        );

        return collect([
            [
                'title' => 'Fetch Data OPD',
                'accent' => '#2563eb',
                'batch' => 'Sinkronisasi berkala',

                'next_run' => $this->nextEveryTwoHours($now),

                'metric' => Opd::query()
                    ->where('status_aktif', true)
                    ->whereNotNull('endpoint_api')
                    ->where('endpoint_api', '!=', '')
                    ->count(),

                'metric_label' => 'OPD aktif dengan endpoint',

                // Ambil fetch OPD terakhir dari log_proses
                'last_run' => LogProses::query()
                    ->where('nama_proses', 'like', 'fetch_berita_%')
                    ->latest('dijalankan_pada')
                    ->first()?->dijalankan_pada,
            ],

            [
                'title' => 'Proses AI',
                'accent' => '#16a34a',
                'batch' => $this->aiBatchName($nextAiRun),

                'next_run' => $nextAiRun,

                'metric' => Berita::query()
                    ->whereNull('ringkasan')
                    ->count(),

                'metric_label' => 'berita menunggu ringkasan AI',

                'last_run' => LogProses::query()
                    ->where('nama_proses', 'process_pending_berita')
                    ->latest('dijalankan_pada')
                    ->first()?->dijalankan_pada,
            ],

            [
                'title' => 'Ringkasan Mingguan',
                'accent' => '#ca8a04',
                'batch' => 'Minggu sore',

                'next_run' => $this->nextWeeklyRun(
                    $now,
                    Carbon::SUNDAY,
                    '18:00'
                ),

                'metric' => Berita::query()
                    ->where(
                        'tanggal_publish',
                        '>=',
                        $now->copy()->subWeek()->toDateString()
                    )
                    ->count(),

                'metric_label' => 'berita dalam 7 hari terakhir',

                'last_run' => LogProses::query()
                    ->where(
                        'nama_proses',
                        'generate_ringkasan_mingguan'
                    )
                    ->latest('dijalankan_pada')
                    ->first()?->dijalankan_pada,
            ],
        ]);
    }

    protected function nextEveryTwoHours(Carbon $now): Carbon
    {
        $next = $now->copy()->startOfHour();

        $hour = $next->hour;

        if ($hour % 2 === 0) {
            if ($next->lessThanOrEqualTo($now)) {
                $next->addHours(2);
            }
        } else {
            $next->addHour();
        }

        return $next;
    }

    protected function nextDailyTime(
        Carbon $now,
        array $times
    ): Carbon {
        foreach ($times as $time) {
            [$hour, $minute] = array_map(
                'intval',
                explode(':', $time)
            );

            $candidate = $now->copy()
                ->setTime($hour, $minute);

            if ($candidate->isFuture()) {
                return $candidate;
            }
        }

        [$hour, $minute] = array_map(
            'intval',
            explode(':', $times[0])
        );

        return $now->copy()
            ->addDay()
            ->setTime($hour, $minute);
    }

    protected function aiBatchName(Carbon $nextRun): string
    {
        return match ($nextRun->format('H:i')) {
            '08:05' => 'Batch pagi',
            '12:05' => 'Batch siang',
            '18:05' => 'Batch sore',
            default => 'Batch berikutnya',
        };
    }

    protected function nextWeeklyRun(
        Carbon $now,
        int $dayOfWeek,
        string $time
    ): Carbon {
        [$hour, $minute] = array_map(
            'intval',
            explode(':', $time)
        );

        $candidate = $now->copy()
            ->next($dayOfWeek)
            ->setTime($hour, $minute);

        if ($now->dayOfWeek === $dayOfWeek) {
            $today = $now->copy()
                ->setTime($hour, $minute);

            if ($today->isFuture()) {
                return $today;
            }
        }

        return $candidate;
    }
}