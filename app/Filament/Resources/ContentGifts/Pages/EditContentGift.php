<?php

namespace App\Filament\Resources\ContentGifts\Pages;

use App\Filament\Resources\ContentGifts\ContentGiftResource;
use App\Models\ContentMultimedia;
use App\Models\ContentGalleryImage;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditContentGift extends EditRecord
{
    protected static string $resource = ContentGiftResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $contentGift = $this->record;
        $multimedia = $contentGift->multimedia;
        
        if ($multimedia) {
            // Agregar datos de multimedia
            $data = array_merge($data, [
                'video_url' => $multimedia->video_url,
                'video_file' => $multimedia->video_file,
                'video_type' => $multimedia->video_type ?? 'direct',
                'audio_url' => $multimedia->audio_url,
                'audio_file' => $multimedia->audio_file,
                'audio_type' => $multimedia->audio_type ?? 'direct',
                'settings' => $multimedia->settings ?? [],
            ]);
            
            // Agregar imágenes de galería
            $galleryImages = $multimedia->galleryImages()
                ->orderBy('sort_order')
                ->orderBy('id')
                ->pluck('image_path')
                ->filter()
                ->values()
                ->toArray();
            
            $data['gallery_images'] = $galleryImages;
        } else {
            // Valores por defecto para multimedia
            $data = array_merge($data, [
                'video_type' => 'direct',
                'audio_type' => 'direct',
                'settings' => ['theme' => 'love'],
                'gallery_images' => [],
            ]);
        }
        
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Separar datos del regalo de los datos multimedia
        $giftData = [
            'dynamic_content_id' => $data['dynamic_content_id'],
            'sender_name' => $data['sender_name'],
            'recipient_name' => $data['recipient_name'],
            'message' => $data['message'],
        ];
        
        $multimediaData = [
            'video_url' => $data['video_url'] ?? null,
            'video_file' => $data['video_file'] ?? null,
            'video_type' => $data['video_type'] ?? 'direct',
            'audio_url' => $data['audio_url'] ?? null,
            'audio_file' => $data['audio_file'] ?? null,
            'audio_type' => $data['audio_type'] ?? 'direct',
            'settings' => $data['settings'] ?? ['theme' => 'love'],
        ];
        
        // Guardar o actualizar multimedia
        $multimedia = $this->record->multimedia;
        if (!$multimedia) {
            $multimedia = ContentMultimedia::create(array_merge($multimediaData, [
                'dynamic_content_id' => $data['dynamic_content_id']
            ]));
        } else {
            $multimedia->update($multimediaData);
        }
        
        // Manejar galería de imágenes
        if (isset($data['gallery_images']) && is_array($data['gallery_images'])) {
            // Eliminar imágenes existentes
            $multimedia->galleryImages()->delete();
            
            // Crear nuevas imágenes
            foreach ($data['gallery_images'] as $index => $imagePath) {
                if ($imagePath) {
                    ContentGalleryImage::create([
                        'content_multimedia_id' => $multimedia->id,
                        'image_path' => $imagePath,
                        'image_url' => null,
                        'type' => ContentGalleryImage::TYPE_UPLOAD,
                        'sort_order' => $index + 1,
                        'alt_text' => "Imagen " . ($index + 1),
                    ]);
                }
            }
        }
        
        // Retornar solo datos del regalo para el modelo principal
        return $giftData;
    }
}
