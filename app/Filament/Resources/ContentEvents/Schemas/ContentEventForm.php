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
                // Campo oculto - se crea automáticamente
                // Select::make('dynamic_content_id')
                //     ->relationship('dynamicContent', 'title')
                //     ->label('Contenido Dinámico Asociado')
                //     ->disabled()
                //     ->helperText('Se crea automáticamente con los datos del evento'),

                TextInput::make('event_organizer')
                    ->label('Organizador del Evento')
                    ->required()
                    ->maxLength(255)
                    ->helperText('Este será el título del evento'),
                TextInput::make('event_location')
                    ->label('Ubicación del Evento')
                    ->maxLength(255),
                DateTimePicker::make('event_start_date')
                    ->label('Fecha de Inicio'),
                DateTimePicker::make('event_end_date')
                    ->label('Fecha de Fin'),
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
