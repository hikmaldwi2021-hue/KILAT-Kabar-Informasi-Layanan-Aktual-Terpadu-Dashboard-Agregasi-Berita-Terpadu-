<?php

namespace App\Filament\Resources\ApiClients\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ApiClientsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_sistem')
                    ->label('Nama Sistem')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('token')
                ->label('Token API')
                ->formatStateUsing(fn ($state) => $state
                    ? substr($state, 0, 8) . '••••••••'
                    : '-'
                )
                ->copyable()
                ->copyableState(fn ($record) => $record->token)
                ->copyMessage('Token berhasil disalin')
                ->copyMessageDuration(1500),

                IconColumn::make('status_aktif')
                    ->label('Status')
                    ->boolean(),

                TextColumn::make('last_used_at')
                    ->label('Terakhir Digunakan')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->placeholder('Belum digunakan'),

                TextColumn::make('createdBy.name')
                    ->label('Dibuat Oleh')
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])

            ->filters([
                //
            ])

            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}