<?php

namespace App\Filament\Resources\ContentGifts\Pages;

use App\Filament\Resources\ContentGifts\ContentGiftResource;
use App\Models\ContentMultimedia;
use App\Models\ContentGalleryImage;
use Filament\Resources\Pages\CreateRecord;

class CreateContentGift extends CreateRecord
{
    protected static string $resource = ContentGiftResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Separar datos del regalo de los datos multimedia
        $giftData = [
            'dynamic_content_id' => $data['dynamic_content_id'],
            'sender_name' => $data['sender_name'],
            'recipient_name' => $data['recipient_name'],
            'message' => $data['message'],
        ];
        
        // Guardar datos multimedia para después
        $this->multimediaData = [
            'video_url' => $data['video_url'] ?? null,
            'video_file' => $data['video_file'] ?? null,
            'video_type' => $data['video_type'] ?? 'direct',
            'audio_url' => $data['audio_url'] ?? null,
            'audio_file' => $data['audio_file'] ?? null,
            'audio_type' => $data['audio_type'] ?? 'direct',
            'settings' => $data['settings'] ?? ['theme' => 'love'],
        ];
        
        $this->galleryImages = $data['gallery_images'] ?? [];
        
        return $giftData;
    }

    protected function afterCreate(): void
    {
        // Crear registro multimedia después de crear el regalo
        if (!empty($this->multimediaData)) {
            $multimedia = ContentMultimedia::create(array_merge($this->multimediaData, [
                'dynamic_content_id' => $this->record->dynamic_content_id
            ]));
            
            // Crear imágenes de galería si existen
            if (!empty($this->galleryImages) && is_array($this->galleryImages)) {
                foreach ($this->galleryImages as $index => $imagePath) {
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
        }
    }

    protected array $multimediaData = [];
    protected array $galleryImages = [];
}
