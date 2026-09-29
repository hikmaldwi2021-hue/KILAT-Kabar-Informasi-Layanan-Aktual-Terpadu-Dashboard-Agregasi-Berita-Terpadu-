<?php

namespace App\Filament\Resources\LogProses\Tables;

use App\Models\LogProses;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LogProsesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_proses')
                    ->label('Proses')
                    ->searchable()
                    ->sortable(),

                BadgeColumn::make('status')
                    ->colors([
                        'success' => 'sukses',
                        'warning' => 'sebagian',
                        'danger' => 'gagal',
                        'gray' => 'kosong',
                    ]),

                TextColumn::make('jumlah_diproses')
                    ->label('Jumlah Diproses')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('keterangan')
                    ->limit(60)
                    ->tooltip(fn ($record) => $record->keterangan),

                TextColumn::make('dijalankan_pada')
                    ->label('Dijalankan Pada (WIB)')
                    ->dateTime('d M Y H:i')
                    ->timezone('Asia/Jakarta')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('nama_proses')
                    ->label('Proses')
                    ->options(fn () => LogProses::query()
                        ->distinct()
                        ->orderBy('nama_proses')
                        ->pluck('nama_proses', 'nama_proses')
                        ->toArray()
                    ),
                SelectFilter::make('status')
                    ->options([
                        'sukses' => 'Sukses',
                        'sebagian' => 'Sebagian Berhasil',
                        'gagal' => 'Gagal',
                        'kosong' => 'Kosong (Tidak Ada Data)',
                    ]),
            ])
            ->recordActions([
                ViewAction::make()->slideOver(),
            ])
            ->defaultSort('dijalankan_pada', 'desc')
            ->poll('30s');
    }
}