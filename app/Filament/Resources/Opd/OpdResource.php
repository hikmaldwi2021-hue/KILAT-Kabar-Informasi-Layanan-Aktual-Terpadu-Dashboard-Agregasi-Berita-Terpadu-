<?php

namespace App\Filament\Resources\Opd;

use App\Filament\Resources\Opd\Pages\CreateOpd;
use App\Filament\Resources\Opd\Pages\EditOpd;
use App\Filament\Resources\Opd\Pages\ListOpd;
use App\Filament\Resources\Opd\Schemas\OpdForm;
use App\Filament\Resources\Opd\Tables\OpdTable;
use App\Models\Opd;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class OpdResource extends Resource
{
    protected static ?string $model = Opd::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static ?string $navigationLabel = 'OPD';

    protected static ?string $modelLabel = 'OPD';

    protected static ?string $pluralModelLabel = 'OPD';

    protected static string|UnitEnum|null $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'nama';

    public static function form(Schema $schema): Schema
    {
        return OpdForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OpdTable::configure($table);
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
            'index' => ListOpd::route('/'),
            'create' => CreateOpd::route('/create'),
            'edit' => EditOpd::route('/{record}/edit'),
        ];
    }
}
