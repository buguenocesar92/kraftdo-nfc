<?php

namespace App\Filament\Resources\ContentMultimedia;

use App\Filament\Resources\ContentMultimedia\Pages\CreateContentMultimedia;
use App\Filament\Resources\ContentMultimedia\Pages\EditContentMultimedia;
use App\Filament\Resources\ContentMultimedia\Pages\ListContentMultimedia;
use App\Filament\Resources\ContentMultimedia\Schemas\ContentMultimediaForm;
use App\Filament\Resources\ContentMultimedia\Tables\ContentMultimediaTable;
use App\Models\ContentMultimedia;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ContentMultimediaResource extends Resource
{
    protected static ?string $model = ContentMultimedia::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;
    
    public static function getNavigationGroup(): ?string
    {
        return 'Configuración';
    }
    
    public static function getNavigationLabel(): string
    {
        return 'Multimedia';
    }
    
    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->can('view_any_dynamic_content') || auth()->user()->can('view_dynamic_content');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create_dynamic_content');
    }

    public static function canView($record): bool
    {
        return auth()->user()->can('view_dynamic_content', $record) || auth()->user()->can('view_any_dynamic_content');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('update_dynamic_content', $record);
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('delete_dynamic_content', $record) || auth()->user()->can('delete_any_dynamic_content');
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()->can('delete_any_dynamic_content');
    }

    public static function form(Schema $schema): Schema
    {
        return ContentMultimediaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ContentMultimediaTable::configure($table)
            ->modifyQueryUsing(fn ($query) => $query->with(['dynamicContent']));
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
            'index' => ListContentMultimedia::route('/'),
            'create' => CreateContentMultimedia::route('/create'),
            'edit' => EditContentMultimedia::route('/{record}/edit'),
        ];
    }
}
