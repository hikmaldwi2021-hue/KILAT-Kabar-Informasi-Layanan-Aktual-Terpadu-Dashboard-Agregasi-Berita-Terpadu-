<?php

namespace App\Filament\Resources\LogProses\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LogProsesInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Ringkasan')
                ->columns(2)
                ->schema([
                    TextEntry::make('nama_proses')->label('Proses'),
                    TextEntry::make('status')
                        ->badge()
                        ->color(fn (string $state) => match ($state) {
                            'sukses' => 'success',
                            'sebagian' => 'warning',
                            'gagal' => 'danger',
                            default => 'gray',
                        }),
                    TextEntry::make('jumlah_diproses')->label('Jumlah Diproses'),
                    TextEntry::make('dijalankan_pada')
                        ->label('Dijalankan Pada')
                        ->dateTime('d M Y H:i:s'),
                    TextEntry::make('keterangan')->columnSpanFull(),
                ]),

            Section::make('Detail Error')
                ->visible(fn ($record) => ! empty($record->detail['error'] ?? null))
                ->schema([
                    TextEntry::make('detail.error')
                        ->label('Pesan Error')
                        ->columnSpanFull()
                        ->color('danger'),
                ]),

            Section::make('Rincian Berita')
                ->visible(fn ($record) => ! empty($record->detail['berita'] ?? null))
                ->schema([
                    RepeatableEntry::make('detail.berita')
                        ->label('')
                        ->schema([
                            TextEntry::make('judul'),
                            TextEntry::make('status')
                                ->badge()
                                ->color(fn (string $state) => $state === 'sukses' ? 'success' : 'danger'),
                        ])
                        ->columns(2),
                ]),
        ]);
    }
}