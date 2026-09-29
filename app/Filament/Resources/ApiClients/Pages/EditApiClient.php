<?php

namespace App\Filament\Resources\ApiClients\Pages;

use App\Filament\Resources\ApiClients\ApiClientResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;

class EditApiClient extends EditRecord
{
    protected static string $resource = ApiClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('regenerateToken')
                ->label('Regenerate Token')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Regenerate Token API?')
                ->modalDescription(
                    'Token lama akan langsung tidak berlaku dan diganti dengan token baru.'
                )
                ->action(function () {
                    $this->record->update([
                        'token' => Str::random(64),
                    ]);

                    Notification::make()
                        ->title('Token berhasil dibuat ulang')
                        ->success()
                        ->send();
                }),

            DeleteAction::make(),
        ];
    }
}