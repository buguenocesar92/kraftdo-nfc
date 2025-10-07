<?php

namespace App\Filament\Resources\UtilityPhones;

use App\Filament\Resources\UtilityPhones\Pages\CreateUtilityPhone;
use App\Filament\Resources\UtilityPhones\Pages\EditUtilityPhone;
use App\Filament\Resources\UtilityPhones\Pages\ListUtilityPhones;
use App\Filament\Resources\UtilityPhones\Schemas\UtilityPhoneForm;
use App\Filament\Resources\UtilityPhones\Tables\UtilityPhonesTable;
use App\Models\UtilityPhone;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class UtilityPhoneResource extends Resource
{
    protected static ?string $model = UtilityPhone::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return UtilityPhoneForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UtilityPhonesTable::configure($table);
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
            'index' => ListUtilityPhones::route('/'),
            'create' => CreateUtilityPhone::route('/create'),
            'edit' => EditUtilityPhone::route('/{record}/edit'),
        ];
    }
}
