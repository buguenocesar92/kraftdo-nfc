<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Resources\Users\Tables\UsersTable;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    public static function getNavigationGroup(): ?string
    {
        return 'Administración';
    }

    public static function getNavigationLabel(): string
    {
        return 'Usuarios';
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->can('view_any_user') || auth()->user()->can('view_user');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create_user');
    }

    public static function canView($record): bool
    {
        return auth()->user()->can('view_user', $record) || auth()->user()->can('view_any_user');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('update_user', $record);
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('delete_user', $record) || auth()->user()->can('delete_any_user');
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()->can('delete_any_user');
    }

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table)
            ->modifyQueryUsing(fn ($query) => $query->with('roles'));
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
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
