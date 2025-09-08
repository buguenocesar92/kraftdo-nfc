<?php

namespace App\Filament\Components;

use App\Models\ContentGalleryImage;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;

class GallerySection
{
    public static function make(): Section
    {
        return Section::make('Galería de Imágenes')
            ->schema([
                Repeater::make('dynamicContent.multimedia.galleryImages')
                    ->label('Imágenes de la Galería')
                    ->schema([
                        Select::make('type')
                            ->label('Tipo de Imagen')
                            ->options(ContentGalleryImage::TYPES)
                            ->default(ContentGalleryImage::TYPE_UPLOAD)
                            ->required()
                            ->live(),
                        
                        FileUpload::make('image_path')
                            ->label('Subir Imagen')
                            ->image()
                            ->maxSize(5 * 1024) // 5MB
                            ->directory('multimedia/gallery')
                            ->visibility('public')
                            ->visible(fn (callable $get) => $get('type') === ContentGalleryImage::TYPE_UPLOAD)
                            ->helperText('Máximo 5MB. JPG, PNG, WebP')
                            ->imageResizeMode('contain')
                            ->imageCropAspectRatio(null)
                            ->imageResizeTargetWidth(1920)
                            ->imageResizeTargetHeight(1080),
                        
                        TextInput::make('image_url')
                            ->label('URL de la Imagen')
                            ->url()
                            ->visible(fn (callable $get) => $get('type') === ContentGalleryImage::TYPE_URL)
                            ->helperText('URL externa de la imagen')
                            ->required(fn (callable $get) => $get('type') === ContentGalleryImage::TYPE_URL),
                        
                        TextInput::make('alt_text')
                            ->label('Texto Alternativo')
                            ->helperText('Descripción para accesibilidad')
                            ->maxLength(255),
                        
                        TextInput::make('caption')
                            ->label('Pie de Foto')
                            ->helperText('Descripción visible de la imagen')
                            ->maxLength(500),
                        
                        TextInput::make('sort_order')
                            ->label('Orden')
                            ->numeric()
                            ->default(0)
                            ->helperText('Número para ordenar (0 = primero)'),
                    ])
                    ->columns(2)
                    ->defaultItems(0)
                    ->addActionLabel('Agregar imagen')
                    ->reorderableWithButtons()
                    ->collapsible()
                    ->itemLabel(fn (array $state): ?string => 
                        $state['caption'] ?? $state['alt_text'] ?? 'Imagen sin título'
                    )
                    ->columnSpanFull(),
            ])
            ->collapsible();
    }
}