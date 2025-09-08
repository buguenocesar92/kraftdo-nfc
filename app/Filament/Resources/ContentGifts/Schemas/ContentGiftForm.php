<?php

namespace App\Filament\Resources\ContentGifts\Schemas;

use App\Models\DynamicContent;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ContentGiftForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('dynamic_content_id')
                    ->relationship(
                        name: 'dynamicContent', 
                        titleAttribute: 'title',
                        modifyQueryUsing: fn ($query) => $query->where('type', DynamicContent::TYPE_GIFT)
                    )
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->title} - {$record->content_id}")
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('sender_name'),
                TextInput::make('recipient_name'),
                Textarea::make('message')
                    ->columnSpanFull(),
            ]);
    }
}
