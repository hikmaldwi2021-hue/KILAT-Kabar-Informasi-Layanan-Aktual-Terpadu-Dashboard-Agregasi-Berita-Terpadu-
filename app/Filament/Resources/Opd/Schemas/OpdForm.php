<?php

namespace App\Filament\Resources\Opd\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class OpdForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama')
                    ->required()
                    ->maxLength(255),

                TextInput::make('kode')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(50),

                TextInput::make('endpoint_api')
                    ->label('Endpoint API')
                    ->url(),
                    

                Toggle::make('status_aktif')
                    ->default(true)
                    ->required(),
            ]);
    }
}
