<?php

namespace App\Filament\Resources\ContentGifts\Schemas;

use App\Filament\Components\MultimediaSection;
use App\Models\DynamicContent;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ContentGiftForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información del Regalo')
                    ->schema([
                        Select::make('dynamic_content_id')
                            ->relationship(
                                name: 'dynamicContent', 
                                titleAttribute: 'title',
                                modifyQueryUsing: fn ($query) => $query->where('type', DynamicContent::TYPE_GIFT)
                            )
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->title} - {$record->content_id}")
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpanFull(),
                        TextInput::make('sender_name')
                            ->label('Nombre del remitente'),
                        TextInput::make('recipient_name')
                            ->label('Nombre del destinatario'),
                        Textarea::make('message')
                            ->label('Mensaje')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                MultimediaSection::make(),
            ]);
    }
}
