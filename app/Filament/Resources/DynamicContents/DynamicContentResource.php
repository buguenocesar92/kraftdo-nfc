<?php

namespace App\Filament\Resources\DynamicContents;

use App\Filament\Resources\DynamicContents\Pages\CreateDynamicContent;
use App\Filament\Resources\DynamicContents\Pages\EditDynamicContent;
use App\Filament\Resources\DynamicContents\Pages\ListDynamicContents;
use App\Filament\Resources\DynamicContents\Schemas\DynamicContentForm;
use App\Filament\Resources\DynamicContents\Tables\DynamicContentsTable;
use App\Models\DynamicContent;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DynamicContentResource extends Resource
{
    protected static ?string $model = DynamicContent::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return DynamicContentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DynamicContentsTable::configure($table);
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
            'index' => ListDynamicContents::route('/'),
            'create' => CreateDynamicContent::route('/create'),
            'edit' => EditDynamicContent::route('/{record}/edit'),
        ];
    }
}
