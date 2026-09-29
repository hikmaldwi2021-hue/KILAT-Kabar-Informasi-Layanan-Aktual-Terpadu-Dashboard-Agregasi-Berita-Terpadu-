<?php

namespace App\Filament\Resources\Kategori;

use App\Filament\Resources\Kategori\Pages\ListKategori;
use App\Filament\Resources\Kategori\Pages\CreateKategori;
use App\Filament\Resources\Kategori\Pages\EditKategori;
use App\Filament\Resources\Kategori\Schemas\KategoriForm;
use App\Filament\Resources\Kategori\Tables\KategoriTable;
use App\Models\Kategori;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class KategoriResource extends Resource
{
    protected static ?string $model = Kategori::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static ?string $modelLabel = 'Kategori';

    protected static string|UnitEnum|null $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 1;

    protected static ?string $pluralModelLabel = 'Kategori';

    protected static ?string $navigationLabel = 'Kategori';

    protected static ?string $recordTitleAttribute = 'nama';

    public static function form(Schema $schema): Schema
    {
        return KategoriForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KategoriTable::configure($table);
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
            'index' => ListKategori::route('/'),
            'create' => CreateKategori::route('/create'),
            'edit' => EditKategori::route('/{record}/edit'),
        ];
    }
}
