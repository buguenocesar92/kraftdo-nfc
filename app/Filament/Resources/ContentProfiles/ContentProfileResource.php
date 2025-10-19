<?php

namespace App\Filament\Resources\ContentProfiles;

use App\Filament\Resources\ContentProfiles\Pages\CreateContentProfile;
use App\Filament\Resources\ContentProfiles\Pages\EditContentProfile;
use App\Filament\Resources\ContentProfiles\Pages\ListContentProfiles;
use App\Filament\Resources\ContentProfiles\Schemas\ContentProfileForm;
use App\Filament\Resources\ContentProfiles\Tables\ContentProfilesTable;
use App\Models\ContentProfile;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ContentProfileResource extends Resource
{
    protected static ?string $model = ContentProfile::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUser;

    public static function getNavigationGroup(): ?string
    {
        return 'Contenido Especializado';
    }

    public static function getNavigationLabel(): string
    {
        return 'Perfiles';
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->can('view_any_content_profiles') || auth()->user()->can('view_content_profiles');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create_content_profiles');
    }

    public static function canView($record): bool
    {
        return auth()->user()->can('view_content_profiles', $record) || auth()->user()->can('view_any_content_profiles');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('update_content_profiles', $record);
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('delete_content_profiles', $record) || auth()->user()->can('delete_any_content_profiles');
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()->can('delete_any_content_profiles');
    }

    public static function form(Schema $schema): Schema
    {
        return ContentProfileForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ContentProfilesTable::configure($table)
            ->modifyQueryUsing(fn ($query) => $query->with(['dynamicContent', 'multimedia', 'galleryImages', 'socialLinks']));
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
            'index' => ListContentProfiles::route('/'),
            'create' => CreateContentProfile::route('/create'),
            'edit' => EditContentProfile::route('/{record}/edit'),
        ];
    }
}
