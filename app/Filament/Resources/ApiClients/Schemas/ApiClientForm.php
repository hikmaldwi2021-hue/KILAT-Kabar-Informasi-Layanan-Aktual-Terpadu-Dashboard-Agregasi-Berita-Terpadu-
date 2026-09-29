<?php

namespace App\Filament\Resources\ApiClients\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ApiClientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama_sistem')
                    ->label('Nama Sistem')
                    ->required()
                    ->maxLength(255),

                TextInput::make('token')
                    ->label('Token API')
                    ->disabled()
                    ->dehydrated(false)
                    ->visibleOn('edit'),

                Toggle::make('status_aktif')
                    ->label('Status Aktif')
                    ->default(true)
                    ->required(),

                TextInput::make('last_used_at')
                    ->label('Terakhir Digunakan')
                    ->disabled()
                    ->dehydrated(false)
                    ->visibleOn('edit'),
            ]);
    }
}