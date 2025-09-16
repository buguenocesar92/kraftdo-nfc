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
    
    public static function getNavigationGroup(): ?string
    {
        return 'Contenido Especializado';
    }
    
    public static function getNavigationLabel(): string
    {
        return 'Restaurantes';
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->can('view_any_content_menu') || auth()->user()->can('view_content_menu');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create_content_menu');
    }

    public static function canView($record): bool
    {
        return auth()->user()->can('view_content_menu', $record) || auth()->user()->can('view_any_content_menu');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('update_content_menu', $record);
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('delete_content_menu', $record) || auth()->user()->can('delete_any_content_menu');
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()->can('delete_any_content_menu');
    }

    public static function form(Schema $schema): Schema
    {
        return ContentMenuForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ContentMenusTable::configure($table)
            ->modifyQueryUsing(fn ($query) => $query->with(['dynamicContent.multimedia']));
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
