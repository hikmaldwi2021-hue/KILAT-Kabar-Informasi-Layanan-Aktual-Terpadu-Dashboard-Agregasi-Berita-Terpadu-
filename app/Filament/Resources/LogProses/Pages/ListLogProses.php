<?php

namespace App\Filament\Resources\LogProses\Pages;

use App\Filament\Resources\LogProses\LogProsesResource;
use App\Filament\Widgets\ProcessCountdownWidget;
use Filament\Resources\Pages\ListRecords;

class ListLogProses extends ListRecords
{
    protected static string $resource = LogProsesResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ProcessCountdownWidget::class,
        ];
    }
}
