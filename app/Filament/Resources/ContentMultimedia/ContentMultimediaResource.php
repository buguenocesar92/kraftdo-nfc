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

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return ContentMultimediaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ContentMultimediaTable::configure($table);
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
