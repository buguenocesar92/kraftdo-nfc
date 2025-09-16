<?php

namespace App\Filament\Resources\ContentProducts;

use App\Filament\Resources\ContentProducts\Pages\CreateContentProduct;
use App\Filament\Resources\ContentProducts\Pages\EditContentProduct;
use App\Filament\Resources\ContentProducts\Pages\ListContentProducts;
use App\Filament\Resources\ContentProducts\Schemas\ContentProductForm;
use App\Filament\Resources\ContentProducts\Tables\ContentProductsTable;
use App\Models\ContentProduct;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ContentProductResource extends Resource
{
    protected static ?string $model = ContentProduct::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    
    public static function getNavigationGroup(): ?string
    {
        return 'Contenido Especializado';
    }
    
    public static function getNavigationLabel(): string
    {
        return 'Productos';
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->can('view_any_content_product') || auth()->user()->can('view_content_product');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create_content_product');
    }

    public static function canView($record): bool
    {
        return auth()->user()->can('view_content_product', $record) || auth()->user()->can('view_any_content_product');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('update_content_product', $record);
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('delete_content_product', $record) || auth()->user()->can('delete_any_content_product');
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()->can('delete_any_content_product');
    }

    public static function form(Schema $schema): Schema
    {
        return ContentProductForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ContentProductsTable::configure($table)
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
            'index' => ListContentProducts::route('/'),
            'create' => CreateContentProduct::route('/create'),
            'edit' => EditContentProduct::route('/{record}/edit'),
        ];
    }
}
