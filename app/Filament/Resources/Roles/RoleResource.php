<?php

namespace App\Filament\Resources\Roles;

use App\Filament\Resources\Roles\Pages\CreateRole;
use App\Filament\Resources\Roles\Pages\EditRole;
use App\Filament\Resources\Roles\Pages\ListRoles;
use App\Filament\Resources\Roles\Schemas\RoleForm;
use App\Filament\Resources\Roles\Tables\RolesTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Spatie\Permission\Models\Role;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    public static function getNavigationGroup(): ?string
    {
        return 'Administración';
    }

    public static function getNavigationLabel(): string
    {
        return 'Roles';
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->can('view_any_roles') || auth()->user()->can('view_roles');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create_roles');
    }

    public static function canView($record): bool
    {
        return auth()->user()->can('view_roles', $record) || auth()->user()->can('view_any_roles');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('update_roles', $record);
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('delete_roles', $record) || auth()->user()->can('delete_any_roles');
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()->can('delete_any_roles');
    }

    public static function form(Schema $schema): Schema
    {
        return RoleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RolesTable::configure($table)
            ->modifyQueryUsing(fn ($query) => $query->withCount(['permissions', 'users']));
    }
    
    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        // NO cargar permissions automáticamente - se cargarán bajo demanda
        return parent::getEloquentQuery();
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
            'index' => ListRoles::route('/'),
            'create' => CreateRole::route('/create'),
            'edit' => EditRole::route('/{record}/edit'),
        ];
    }
}
