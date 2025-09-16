<?php

namespace App\Filament\Resources\ContentProfiles\Pages;

use App\Filament\Resources\ContentProfiles\ContentProfileResource;
use App\Models\ContentMultimedia;
use App\Models\ContentSocialLink;
use App\Models\ContentGalleryImage;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

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
        // Procesar paleta de colores
        if (isset($data['color_palette'])) {
            // La paleta de colores se guarda como JSON
            $this->record->color_palette = $data['color_palette'];
            unset($data['color_palette']);
        }
        
        return $data;
    }

    protected function afterSave(): void
    {
        $data = $this->form->getState();
        
        // Manejar contenido multimedia
        $this->handleMultimediaContent($data);
        
        // Manejar enlaces sociales
        $this->handleSocialLinks($data);
        
        Notification::make()
            ->title('Perfil actualizado')
            ->body('Se ha actualizado correctamente el perfil y su contenido relacionado.')
            ->success()
            ->send();
    }

    private function handleMultimediaContent(array $data): void
    {
        $contentMultimedia = ContentMultimedia::firstOrCreate(
            ['dynamic_content_id' => $this->record->dynamic_content_id],
            [
                'video_type' => 'direct',
                'audio_type' => 'direct',
                'settings' => [],
            ]
        );

        // Actualizar campos de multimedia
        $multimediaData = [
            'video_url' => $data['video_url'] ?? null,
            'video_file' => $data['video_file'] ?? null,
            'video_type' => $data['video_type'] ?? 'direct',
            'settings' => array_merge($contentMultimedia->settings ?? [], $data['settings'] ?? []),
        ];
        
        $contentMultimedia->update($multimediaData);

        // Manejar galería de imágenes
        if (isset($data['gallery_images']) && is_array($data['gallery_images'])) {
            // Eliminar imágenes existentes
            ContentGalleryImage::where('content_multimedia_id', $contentMultimedia->id)->delete();
            
            // Crear nuevas imágenes
            foreach ($data['gallery_images'] as $index => $imagePath) {
                if (!empty($imagePath)) {
                    ContentGalleryImage::create([
                        'content_multimedia_id' => $contentMultimedia->id,
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

    private function handleSocialLinks(array $data): void
    {
        if (isset($data['social_links']) && is_array($data['social_links'])) {
            // Eliminar enlaces existentes
            ContentSocialLink::where('dynamic_content_id', $this->record->dynamic_content_id)->delete();
            
            // Crear nuevos enlaces
            foreach ($data['social_links'] as $index => $link) {
                if (!empty($link['platform'])) {
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
    }
}
