<?php

namespace App\Filament\Resources\Roles;

use App\Filament\Resources\Roles\Pages\CreateRole;
use App\Filament\Resources\Roles\Pages\EditRole;
use App\Filament\Resources\Roles\Pages\ListRoles;
use App\Filament\Resources\Roles\Schemas\RoleForm;
use App\Filament\Resources\Roles\Tables\RolesTable;
use Spatie\Permission\Models\Role;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

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
        return auth()->user()->can('view_any_role') || auth()->user()->can('view_role');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create_role');
    }

    public static function canView($record): bool
    {
        return auth()->user()->can('view_role', $record) || auth()->user()->can('view_any_role');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('update_role', $record);
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('delete_role', $record) || auth()->user()->can('delete_any_role');
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()->can('delete_any_role');
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
