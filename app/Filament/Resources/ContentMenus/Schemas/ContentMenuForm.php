<?php

namespace App\Filament\Resources\ContentMenus\Schemas;

use App\Models\DynamicContent;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ContentMenuForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('dynamic_content_id')
                    ->relationship(
                        name: 'dynamicContent',
                        titleAttribute: 'title',
                        modifyQueryUsing: fn ($query) => $query->where('type', DynamicContent::TYPE_BUSINESS)->where('content_businesses.business_type', 'restaurant')
                    )
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->title} - {$record->content_id}")
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('restaurant_name'),
                TextInput::make('restaurant_phone')
                    ->tel(),
                Textarea::make('restaurant_address')
                    ->columnSpanFull(),
                TextInput::make('restaurant_hours'),
            ]);
    }
}
