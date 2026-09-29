<?php

namespace App\Filament\Resources\ApiClients\Pages;

use App\Filament\Resources\ApiClients\ApiClientResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;


class CreateApiClient extends CreateRecord
{
    protected static string $resource = ApiClientResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['token'] = Str::random(64);
        $data['created_by'] = Auth::id();
        $data['status_aktif'] = $data['status_aktif'] ?? true;

        return $data;
    }
}