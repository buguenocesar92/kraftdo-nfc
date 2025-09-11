<?php

namespace App\Filament\Resources\ContentProfiles\Schemas;

use App\Filament\Components\GallerySection;
use App\Filament\Components\MultimediaSection;
use App\Filament\Components\SocialLinksSection;
use App\Models\DynamicContent;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\ColorPicker;
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
                        
                        TextInput::make('name')
                            ->label('Nombre completo')
                            ->required()
                            ->maxLength(255)
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

                Section::make('Paleta de Colores')
                    ->description('Personaliza los colores del perfil')
                    ->schema([
                        ColorPicker::make('color_palette.primary')
                            ->label('Color Primario')
                            ->default('#3B82F6')
                            ->helperText('Color principal del gradiente de fondo'),
                            
                        ColorPicker::make('color_palette.secondary')
                            ->label('Color Secundario')
                            ->default('#8B5CF6')
                            ->helperText('Color secundario del gradiente'),
                            
                        ColorPicker::make('color_palette.accent')
                            ->label('Color Terciario')
                            ->default('#EC4899')
                            ->helperText('Color terciario del gradiente'),
                    ])
                    ->columns(3)
                    ->collapsible(),

                MultimediaSection::make(),

                GallerySection::make(),

                SocialLinksSection::make(),
            ]);
    }
}
