<?php

namespace App\Filament\Resources\ContentBusinessGroups;

use App\Filament\Resources\ContentBusinessGroups\Pages\CreateContentBusinessGroup;
use App\Filament\Resources\ContentBusinessGroups\Pages\EditContentBusinessGroup;
use App\Filament\Resources\ContentBusinessGroups\Pages\ListContentBusinessGroups;
use App\Filament\Resources\ContentBusinessGroups\Schemas\ContentBusinessGroupForm;
use App\Filament\Resources\ContentBusinessGroups\Tables\ContentBusinessGroupsTable;
use App\Models\ContentBusinessGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ContentBusinessGroupResource extends Resource
{
    protected static ?string $model = ContentBusinessGroup::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingStorefront;

    public static function canAccess(): bool
    {
        return auth()->user()?->can('view_content_business_groups') ?? false;
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Contenido Especializado';
    }

    public static function getNavigationLabel(): string
    {
        return 'Grupos de Negocios';
    }

    public static function getModelLabel(): string
    {
        return 'Grupo de Negocios';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Grupos de Negocios';
    }

    public static function form(Schema $schema): Schema
    {
        return ContentBusinessGroupForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ContentBusinessGroupsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\MemberBusinessesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContentBusinessGroups::route('/'),
            'create' => CreateContentBusinessGroup::route('/create'),
            'edit' => EditContentBusinessGroup::route('/{record}/edit'),
        ];
    }
}
