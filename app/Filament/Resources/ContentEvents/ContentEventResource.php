<?php

namespace App\Filament\Resources\ContentEvents;

use App\Filament\Resources\ContentEvents\Pages\CreateContentEvent;
use App\Filament\Resources\ContentEvents\Pages\EditContentEvent;
use App\Filament\Resources\ContentEvents\Pages\ListContentEvents;
use App\Filament\Resources\ContentEvents\Schemas\ContentEventForm;
use App\Filament\Resources\ContentEvents\Tables\ContentEventsTable;
use App\Models\ContentEvent;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ContentEventResource extends Resource
{
    protected static ?string $model = ContentEvent::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    public static function getNavigationGroup(): ?string
    {
        return 'Contenido Especializado';
    }

    public static function getNavigationLabel(): string
    {
        return 'Eventos';
    }

    public static function getNavigationSort(): ?int
    {
        return 7;
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->can('view_any_content_event') || auth()->user()->can('view_content_event');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create_content_event');
    }

    public static function canView($record): bool
    {
        return auth()->user()->can('view_content_event', $record) || auth()->user()->can('view_any_content_event');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('update_content_event', $record);
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('delete_content_event', $record) || auth()->user()->can('delete_any_content_event');
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()->can('delete_any_content_event');
    }

    public static function form(Schema $schema): Schema
    {
        return ContentEventForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ContentEventsTable::configure($table)
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
            'index' => ListContentEvents::route('/'),
            'create' => CreateContentEvent::route('/create'),
            'edit' => EditContentEvent::route('/{record}/edit'),
        ];
    }
}
