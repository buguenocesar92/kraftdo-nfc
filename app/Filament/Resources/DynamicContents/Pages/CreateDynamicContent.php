<?php

namespace App\Filament\Resources\DynamicContents\Pages;

use App\Filament\Resources\DynamicContents\DynamicContentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDynamicContent extends CreateRecord
{
    protected static string $resource = DynamicContentResource::class;

    protected function afterCreate(): void
    {
        $data = $this->form->getState();
        $record = $this->record;

        // Guardar multimedia - solo si no se seleccionó uno existente y hay nuevos datos
        if (! $data['multimedia_id'] && isset($data['multimedia']) && ! empty(array_filter($data['multimedia']))) {
            $record->createOrUpdateMultimedia($data['multimedia']);
        }

        // Guardar según tipo - solo si no se seleccionó uno existente y hay nuevos datos
        switch ($record->type) {
            case 'GIFT':
                if (! $data['gift_id'] && isset($data['gift']) && ! empty(array_filter($data['gift']))) {
                    $record->createOrUpdateGift($data['gift']);
                }

                break;

            case 'MENU':
                if (! $data['menu_id'] && isset($data['menu']) && ! empty(array_filter($data['menu']))) {
                    $record->createOrUpdateMenu($data['menu']);
                }

                break;

            case 'PROFILE':
                if (! $data['profile_id'] && isset($data['profile']) && ! empty(array_filter($data['profile']))) {
                    $record->createOrUpdateProfile($data['profile']);
                }

                break;

            case 'EVENT':
                if (! $data['event_id'] && isset($data['event']) && ! empty(array_filter($data['event']))) {
                    $record->createOrUpdateEvent($data['event']);
                }

                break;

            case 'PRODUCT':
                if (! $data['product_id'] && isset($data['product']) && ! empty(array_filter($data['product']))) {
                    $record->createOrUpdateProduct($data['product']);
                }

                break;

            case 'TOURIST':
                if (! $data['tourist_id'] && isset($data['tourist']) && ! empty(array_filter($data['tourist']))) {
                    $record->createOrUpdateTourist($data['tourist']);
                }

                break;
        }

        // Guardar social links si es perfil (solo si no se seleccionó perfil existente)
        if ($record->type === 'PROFILE' && ! $data['profile_id'] && isset($data['socialLinks'])) {
            foreach ($data['socialLinks'] as $link) {
                if (! empty(array_filter($link))) {
                    $record->socialLinks()->create($link);
                }
            }
        }

        // Guardar skills si es perfil (solo si no se seleccionó perfil existente)
        if ($record->type === 'PROFILE' && ! $data['profile_id'] && isset($data['skills'])) {
            foreach ($data['skills'] as $skill) {
                if (! empty(array_filter($skill))) {
                    $record->skills()->create($skill);
                }
            }
        }
    }
}
