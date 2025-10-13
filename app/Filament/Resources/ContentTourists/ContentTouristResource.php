<?php

namespace App\Filament\Resources\ContentTourists;

use App\Filament\Resources\ContentTourists\Pages\CreateContentTourist;
use App\Filament\Resources\ContentTourists\Pages\EditContentTourist;
use App\Filament\Resources\ContentTourists\Pages\ListContentTourists;
use App\Filament\Resources\ContentTourists\Schemas\ContentTouristForm;
use App\Filament\Resources\ContentTourists\Tables\ContentTouristsTable;
use App\Models\ContentTourist;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ContentTouristResource extends Resource
{
    protected static ?string $model = ContentTourist::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMapPin;
    
    public static function getNavigationGroup(): ?string
    {
        return 'Contenido Especializado';
    }
    
    public static function getNavigationLabel(): string
    {
        return 'Lugares Turísticos';
    }
    
    public static function getNavigationSort(): ?int
    {
        return 5;
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->can('view_any_content_tourist') || auth()->user()->can('view_content_tourist');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create_content_tourist');
    }

    public static function canView($record): bool
    {
        return auth()->user()->can('view_content_tourist', $record) || auth()->user()->can('view_any_content_tourist');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('update_content_tourist', $record);
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('delete_content_tourist', $record) || auth()->user()->can('delete_any_content_tourist');
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()->can('delete_any_content_tourist');
    }

    public static function form(Schema $schema): Schema
    {
        return ContentTouristForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ContentTouristsTable::configure($table)
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
            'index' => ListContentTourists::route('/'),
            'create' => CreateContentTourist::route('/create'),
            'edit' => EditContentTourist::route('/{record}/edit'),
        ];
    }
}
