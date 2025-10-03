<?php

namespace App\Filament\Resources\ContentBusinesses;

use App\Filament\Resources\ContentBusinesses\Pages\CreateContentBusiness;
use App\Filament\Resources\ContentBusinesses\Pages\EditContentBusiness;
use App\Filament\Resources\ContentBusinesses\Pages\ListContentBusinesses;
use App\Filament\Resources\ContentBusinesses\Schemas\ContentBusinessForm;
use App\Filament\Resources\ContentBusinesses\Tables\ContentBusinessesTable;
use App\Models\ContentBusiness;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ContentBusinessResource extends Resource
{
    protected static ?string $model = ContentBusiness::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;
    
    public static function getNavigationGroup(): ?string
    {
        return 'Contenido NFC';
    }
    
    public static function getNavigationLabel(): string
    {
        return 'Negocios/Ferias';
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->can('view_any_content_business') || auth()->user()->can('view_content_business');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create_content_business');
    }

    public static function canView($record): bool
    {
        return auth()->user()->can('view_content_business', $record) || auth()->user()->can('view_any_content_business');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('update_content_business', $record);
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('delete_content_business', $record) || auth()->user()->can('delete_any_content_business');
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()->can('delete_any_content_business');
    }

    public static function form(Schema $schema): Schema
    {
        return ContentBusinessForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ContentBusinessesTable::configure($table)
            ->modifyQueryUsing(fn ($query) => $query->with(['dynamicContent', 'multimedia', 'galleryImages', 'socialLinks', 'products']));
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
            'index' => ListContentBusinesses::route('/'),
            'create' => CreateContentBusiness::route('/create'),
            'edit' => EditContentBusiness::route('/{record}/edit'),
        ];
    }
}