<?php

namespace App\Filament\Resources\ContentProfiles\Pages;

use App\Filament\Resources\ContentProfiles\ContentProfileResource;
use App\Models\ContentGalleryImage;
use App\Models\ContentMultimedia;
use App\Models\ContentSocialLink;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditContentProfile extends EditRecord
{
    protected static string $resource = ContentProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Asegurar que los nuevos campos estén incluidos
        $data['profession'] = $this->record->profession;
        $data['company'] = $this->record->company;
        $data['location'] = $this->record->location;
        $data['contact_info'] = $this->record->contact_info;

        // Cargar contenido multimedia relacionado
        $contentMultimedia = ContentMultimedia::where('dynamic_content_id', $this->record->dynamic_content_id)->first();

        if ($contentMultimedia) {
            // Agregar datos de multimedia
            $data = array_merge($data, $contentMultimedia->toArray());

            // Cargar imágenes de galería
            $galleryImages = ContentGalleryImage::where('content_multimedia_id', $contentMultimedia->id)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->pluck('image_path')
                ->filter()
                ->values()
                ->toArray();
            $data['gallery_images'] = $galleryImages;
        }

        // Cargar enlaces sociales
        $socialLinks = ContentSocialLink::where('dynamic_content_id', $this->record->dynamic_content_id)
            ->ordered()
            ->get()
            ->map(function ($link) {
                return [
                    'platform' => $link->platform,
                    'url' => $link->url,
                    'username' => $link->username,
                ];
            })
            ->toArray();
        $data['social_links'] = $socialLinks;

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
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

        $multimediaData = [
            'video_url' => $data['video_url'] ?? null,
            'video_file' => $data['video_file'] ?? null,
            'video_type' => $data['video_type'] ?? 'direct',
            'settings' => $data['settings'] ?? [],
        ];

        // Guardar o actualizar multimedia inmediatamente (como en ContentGift)
        $multimedia = $this->record->multimedia;
        if (!$multimedia) {
            $multimedia = ContentMultimedia::create(array_merge($multimediaData, [
                'dynamic_content_id' => $data['dynamic_content_id'],
            ]));
        } else {
            $multimedia->update($multimediaData);
        }

        // Manejar galería de imágenes inmediatamente
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
                        'alt_text' => "Imagen de perfil " . ($index + 1),
                    ]);
                }
            }
        }

        // Manejar enlaces sociales inmediatamente
        if (isset($data['social_links']) && is_array($data['social_links'])) {
            // Eliminar enlaces existentes
            ContentSocialLink::where('dynamic_content_id', $data['dynamic_content_id'])->delete();

            // Crear nuevos enlaces
            foreach ($data['social_links'] as $index => $link) {
                if (!empty($link['platform'])) {
                    ContentSocialLink::create([
                        'dynamic_content_id' => $data['dynamic_content_id'],
                        'platform' => $link['platform'],
                        'username' => $link['username'] ?? '',
                        'url' => $link['url'] ?? '',
                        'sort_order' => $index + 1,
                    ]);
                }
            }
        }

        // Retornar solo datos del perfil para el modelo principal
        return $profileData;
    }

    protected function afterSave(): void
    {
        Notification::make()
            ->title('Perfil actualizado')
            ->body('Se ha actualizado correctamente el perfil y su contenido relacionado.')
            ->success()
            ->send();
    }

}
