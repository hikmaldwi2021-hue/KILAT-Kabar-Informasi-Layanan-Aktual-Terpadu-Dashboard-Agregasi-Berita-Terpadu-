<?php

namespace App\Filament\Resources\Opd\Pages;

use App\Filament\Resources\Opd\OpdResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditOpd extends EditRecord
{
    protected static string $resource = OpdResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
