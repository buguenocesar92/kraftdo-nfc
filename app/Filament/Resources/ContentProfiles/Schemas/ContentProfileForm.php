<?php

namespace App\Filament\Resources\ContentProfiles\Schemas;

use App\Filament\Components\MultimediaSection;
use App\Models\DynamicContent;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ContentProfileForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información del Perfil')
                    ->schema([
                        Select::make('dynamic_content_id')
                            ->relationship(
                                name: 'dynamicContent', 
                                titleAttribute: 'title',
                                modifyQueryUsing: fn ($query) => $query->where('type', DynamicContent::TYPE_PROFILE)
                            )
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->title} - {$record->content_id}")
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpanFull(),
                        TextInput::make('contact_email')
                            ->label('Email de contacto')
                            ->email(),
                        TextInput::make('contact_phone')
                            ->label('Teléfono de contacto')
                            ->tel(),
                        TextInput::make('contact_website')
                            ->label('Sitio web')
                            ->url(),
                        Textarea::make('bio')
                            ->label('Biografía')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                MultimediaSection::make(),
            ]);
    }
}
