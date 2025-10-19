<?php

namespace App\Filament\Resources\ContentProfiles\Pages;

use App\Filament\Resources\ContentProfiles\ContentProfileResource;
use App\Models\ContentGalleryImage;
use App\Models\ContentMultimedia;
use App\Models\ContentSocialLink;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateContentProfile extends CreateRecord
{
    protected static string $resource = ContentProfileResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Procesar paleta de colores
        if (isset($data['color_palette'])) {
            // Se guardará directamente en el modelo ContentProfile
        }

        // Separar datos del perfil de los datos relacionados
        $profileData = [
            'dynamic_content_id' => $data['dynamic_content_id'],
            'name' => $data['name'],
            'bio' => $data['bio'] ?? '',
            'profession' => $data['profession'] ?? '',
            'company' => $data['company'] ?? '',
            'location' => $data['location'] ?? '',
            'contact_info' => $data['contact_info'] ?? '',
            'contact_email' => $data['contact_email'] ?? '',
            'contact_phone' => $data['contact_phone'] ?? '',
            'contact_website' => $data['contact_website'] ?? '',
            'color_palette' => $data['color_palette'] ?? null,
        ];

        // Guardar datos relacionados para después
        $this->multimediaData = [
            'video_url' => $data['video_url'] ?? null,
            'video_file' => $data['video_file'] ?? null,
            'video_type' => $data['video_type'] ?? 'direct',
            'settings' => $data['settings'] ?? [],
        ];

        $this->socialLinks = $data['social_links'] ?? [];
        $this->galleryImages = $data['gallery_images'] ?? [];

        return $profileData;
    }

    protected function afterCreate(): void
    {
        // Crear registro multimedia después de crear el perfil
        if (! empty($this->multimediaData)) {
            $multimedia = ContentMultimedia::create(array_merge($this->multimediaData, [
                'dynamic_content_id' => $this->record->dynamic_content_id,
            ]));

            // Crear imágenes de galería si existen
            if (! empty($this->galleryImages) && is_array($this->galleryImages)) {
                foreach ($this->galleryImages as $index => $imagePath) {
                    if ($imagePath) {
                        ContentGalleryImage::create([
                            'content_multimedia_id' => $multimedia->id,
                            'image_path' => $imagePath,
                            'image_url' => null,
                            'type' => ContentGalleryImage::TYPE_UPLOAD,
                            'sort_order' => $index + 1,
                            'alt_text' => "Imagen de perfil " . ($index + 1),
                        ]);
                    }
                }
            }
        }

        // Crear enlaces sociales si existen
        if (! empty($this->socialLinks) && is_array($this->socialLinks)) {
            foreach ($this->socialLinks as $index => $link) {
                if (! empty($link['platform'])) {
                    ContentSocialLink::create([
                        'dynamic_content_id' => $this->record->dynamic_content_id,
                        'platform' => $link['platform'],
                        'username' => $link['username'] ?? '',
                        'url' => $link['url'] ?? '',
                        'sort_order' => $index + 1,
                    ]);
                }
            }
        }

        Notification::make()
            ->title('Perfil creado')
            ->body('Se ha creado correctamente el perfil con todo su contenido relacionado.')
            ->success()
            ->send();
    }

    protected array $multimediaData = [];
    protected array $socialLinks = [];
    protected array $galleryImages = [];
}
