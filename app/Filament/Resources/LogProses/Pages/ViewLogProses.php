<?php

namespace App\Filament\Resources\LogProses\Pages;

use App\Filament\Resources\LogProses\LogProsesResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewLogProses extends ViewRecord
{
    protected static string $resource = LogProsesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
