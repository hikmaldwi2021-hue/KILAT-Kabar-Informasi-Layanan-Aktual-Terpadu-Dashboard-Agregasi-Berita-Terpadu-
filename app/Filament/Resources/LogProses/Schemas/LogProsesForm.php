<?php

namespace App\Filament\Resources\LogProses\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class LogProsesForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama_proses')
                    ->required(),
                TextInput::make('status')
                    ->required(),
                TextInput::make('jumlah_diproses')
                    ->required()
                    ->numeric()
                    ->default(0),
                Textarea::make('keterangan')
                    ->columnSpanFull(),
                DateTimePicker::make('dijalankan_pada')
                    ->label('Dijalankan Pada (WIB)')
                    ->timezone('Asia/Jakarta')
                    ->required(),
            ]);
    }
}
