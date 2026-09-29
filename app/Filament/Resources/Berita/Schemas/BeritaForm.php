<?php

namespace App\Filament\Resources\Berita\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BeritaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('opd_id')
                    ->label('OPD')
                    ->relationship('opd', 'nama')
                    ->required()
                    ->searchable()
                    ->preload(),

                TextInput::make('source_id')
                    ->label('Source ID (dari API OPD)')
                    ->required()
                    ->maxLength(255),

                TextInput::make('judul')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                Textarea::make('isi')
                    ->required()
                    ->rows(6)
                    ->columnSpanFull(),

                Textarea::make('ringkasan')
                    ->rows(3)
                    ->columnSpanFull()
                    ->helperText('Diisi otomatis oleh AI. Bisa disunting manual jika perlu.'),

                Select::make('kategori_id')
                    ->label('Kategori')
                    ->relationship('kategori', 'nama')
                    ->searchable()
                    ->preload()
                    ->helperText('Kategori final yang ditampilkan (bisa hasil AI atau koreksi admin).'),

                Select::make('kategori_asal_ai')
                    ->label('Kategori Saran AI (awal)')
                    ->relationship('kategoriAsalAi', 'nama')
                    ->disabled()
                    ->dehydrated(false)
                    ->helperText('Kategori awal yang disarankan AI, tidak bisa diubah manual.'),

                Toggle::make('dikoreksi_manual')
                    ->label('Dikoreksi manual?')
                    ->disabled()
                    ->dehydrated(false),

                DatePicker::make('tanggal_publish')
                    ->required(),

                TextInput::make('link_asal')
                    ->label('Link asli')
                    ->url()
                    ->maxLength(255)
                    ->columnSpanFull(),
            ]);
    }
}