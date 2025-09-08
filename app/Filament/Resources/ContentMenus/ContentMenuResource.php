<?php

namespace App\Filament\Resources\ContentMenus;

use App\Filament\Resources\ContentMenus\Pages\CreateContentMenu;
use App\Filament\Resources\ContentMenus\Pages\EditContentMenu;
use App\Filament\Resources\ContentMenus\Pages\ListContentMenus;
use App\Filament\Resources\ContentMenus\Schemas\ContentMenuForm;
use App\Filament\Resources\ContentMenus\Tables\ContentMenusTable;
use App\Models\ContentMenu;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ContentMenuResource extends Resource
{
    protected static ?string $model = ContentMenu::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return ContentMenuForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ContentMenusTable::configure($table);
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
            'index' => ListContentMenus::route('/'),
            'create' => CreateContentMenu::route('/create'),
            'edit' => EditContentMenu::route('/{record}/edit'),
        ];
    }
}
