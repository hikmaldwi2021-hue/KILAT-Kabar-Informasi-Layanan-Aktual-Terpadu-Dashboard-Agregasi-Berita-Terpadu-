<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class Pemantauan extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;
    protected static ?string $navigationLabel = 'Pemantauan';
    protected static ?string $title = 'Pemantauan Aktivitas OPD';

    protected string $view = 'filament.pages.pemantauan';

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\BeritaPerOpdChart::class,
            \App\Filament\Widgets\OpdProgressWidget::class,
        ];
    }
}