<?php

namespace App\Filament\Resources\Berita\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class BeritaTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('opd.nama')
                    ->label('OPD')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('judul')
                    ->searchable()
                    ->limit(50),

                TextColumn::make('kategori.nama')
                    ->label('Kategori')
                    ->badge()
                    ->sortable(),

                IconColumn::make('dikoreksi_manual')
                    ->label('Koreksi')
                    ->boolean(),

                TextColumn::make('tanggal_publish')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('source_id')
                    ->label('Source ID')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('link_asal')
                    ->label('Link asli')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Ditarik pada')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('opd_id')
                    ->label('OPD')
                    ->relationship('opd', 'nama'),

                SelectFilter::make('kategori_id')
                    ->label('Kategori')
                    ->relationship('kategori', 'nama'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('tanggal_publish', 'desc');
    }
}