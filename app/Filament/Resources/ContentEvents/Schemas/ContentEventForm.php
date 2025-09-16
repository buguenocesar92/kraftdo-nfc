<?php

namespace App\Filament\Resources\ContentEvents\Schemas;

use App\Models\DynamicContent;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ContentEventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('dynamic_content_id')
                    ->relationship(
                        name: 'dynamicContent', 
                        titleAttribute: 'title',
                        modifyQueryUsing: fn ($query) => $query->where('type', DynamicContent::TYPE_EVENT)
                    )
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->title} - {$record->content_id}")
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('event_location'),
                DateTimePicker::make('event_start_date'),
                DateTimePicker::make('event_end_date'),
                TextInput::make('event_organizer'),
                TextInput::make('ticket_price')
                    ->numeric(),
                TextInput::make('ticket_currency')
                    ->required()
                    ->default('USD'),
                TextInput::make('registration_url')
                    ->url(),
            ]);
    }
}
