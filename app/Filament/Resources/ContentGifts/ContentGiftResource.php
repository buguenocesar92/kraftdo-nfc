<?php

namespace App\Filament\Resources\ContentGifts;

use App\Filament\Resources\ContentGifts\Pages\CreateContentGift;
use App\Filament\Resources\ContentGifts\Pages\EditContentGift;
use App\Filament\Resources\ContentGifts\Pages\ListContentGifts;
use App\Filament\Resources\ContentGifts\Schemas\ContentGiftForm;
use App\Filament\Resources\ContentGifts\Tables\ContentGiftsTable;
use App\Models\ContentGift;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ContentGiftResource extends Resource
{
    protected static ?string $model = ContentGift::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedGift;

    public static function getNavigationGroup(): ?string
    {
        return 'Contenido Especializado';
    }

    public static function getNavigationLabel(): string
    {
        return 'Regalos';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->can('view_any_content_gifts') || auth()->user()->can('view_content_gifts');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create_content_gifts');
    }

    public static function canView($record): bool
    {
        return auth()->user()->can('view_content_gifts', $record) || auth()->user()->can('view_any_content_gifts');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('update_content_gifts', $record);
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('delete_content_gifts', $record) || auth()->user()->can('delete_any_content_gifts');
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()->can('delete_any_content_gifts');
    }

    public static function form(Schema $schema): Schema
    {
        return ContentGiftForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ContentGiftsTable::configure($table)
            ->modifyQueryUsing(fn ($query) => $query->with(['dynamicContent', 'multimedia', 'galleryImages']));
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
            'index' => ListContentGifts::route('/'),
            'create' => CreateContentGift::route('/create'),
            'edit' => EditContentGift::route('/{record}/edit'),
        ];
    }
}
