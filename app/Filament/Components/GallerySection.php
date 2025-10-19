<?php

namespace App\Filament\Components;

use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;

class GallerySection
{
    public static function make(): Section
    {
        return Section::make('Galería de Imágenes')
            ->schema([
                FileUpload::make('gallery_images')
                    ->label('Imágenes de la Galería')
                    ->image()
                    ->multiple()
                    ->maxFiles(10)
                    ->maxSize(5 * 1024) // 5MB por imagen
                    ->directory('multimedia/gallery')
                    ->visibility('public')
                    ->helperText('Máximo 10 imágenes, 5MB cada una. JPG, PNG, WebP')
                    ->imageResizeMode('contain')
                    ->imageCropAspectRatio(null)
                    ->imageResizeTargetWidth(1920)
                    ->imageResizeTargetHeight(1080)
                    ->reorderable()
                    ->columnSpanFull(),
            ])
            ->collapsible();
    }
}
