<?php

namespace App\Filament\Resources\LogProses\Pages;

use App\Filament\Resources\LogProses\LogProsesResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditLogProses extends EditRecord
{
    protected static string $resource = LogProsesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
