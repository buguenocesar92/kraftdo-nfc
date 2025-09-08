<?php

namespace App\Filament\Resources\DynamicContents;

use App\Filament\Resources\DynamicContents\Pages\CreateDynamicContent;
use App\Filament\Resources\DynamicContents\Pages\EditDynamicContent;
use App\Filament\Resources\DynamicContents\Pages\ListDynamicContents;
use App\Filament\Resources\DynamicContents\Schemas\DynamicContentFormSimple;
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
    
    public static function getNavigationGroup(): ?string
    {
        return 'Contenido Principal';
    }
    
    public static function getNavigationLabel(): string
    {
        return 'Contenido Dinámico';
    }
    
    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function form(Schema $schema): Schema
    {
        return DynamicContentFormSimple::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DynamicContentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            // Relaciones con las tablas normalizadas se manejan en el formulario principal
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
