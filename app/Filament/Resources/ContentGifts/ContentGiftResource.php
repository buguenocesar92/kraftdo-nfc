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

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    
    public static function getNavigationGroup(): ?string
    {
        return 'Contenido Especializado';
    }
    
    public static function getNavigationLabel(): string
    {
        return 'Regalos';
    }

    public static function form(Schema $schema): Schema
    {
        return ContentGiftForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ContentGiftsTable::configure($table)
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
            'index' => ListContentGifts::route('/'),
            'create' => CreateContentGift::route('/create'),
            'edit' => EditContentGift::route('/{record}/edit'),
        ];
    }
}
