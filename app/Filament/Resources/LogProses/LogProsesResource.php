<?php

namespace App\Filament\Resources\LogProses;


use App\Filament\Resources\LogProses\Pages\ListLogProses;
use App\Filament\Resources\LogProses\Schemas\LogProsesForm;
use App\Filament\Resources\LogProses\Schemas\LogProsesInfolist;
use App\Filament\Resources\LogProses\Tables\LogProsesTable;
use App\Models\LogProses;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class LogProsesResource extends Resource
{
    protected static ?string $model = LogProses::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    protected static ?string $recordTitleAttribute = 'LogProses';

    public static function form(Schema $schema): Schema
    {
        return LogProsesForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return LogProsesInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LogProsesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLogProses::route('/'),
        ];
    }
}
