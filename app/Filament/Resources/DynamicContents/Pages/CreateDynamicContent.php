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
        
        // Guardar multimedia
        if (isset($data['multimedia']) && !empty(array_filter($data['multimedia']))) {
            $record->createOrUpdateMultimedia($data['multimedia']);
        }
        
        // Guardar según tipo
        switch ($record->type) {
            case 'GIFT':
                if (isset($data['gift']) && !empty(array_filter($data['gift']))) {
                    $record->createOrUpdateGift($data['gift']);
                }
                break;
                
            case 'MENU':
                if (isset($data['menu']) && !empty(array_filter($data['menu']))) {
                    $record->createOrUpdateMenu($data['menu']);
                }
                break;
                
            case 'PROFILE':
                if (isset($data['profile']) && !empty(array_filter($data['profile']))) {
                    $record->createOrUpdateProfile($data['profile']);
                }
                break;
                
            case 'EVENT':
                if (isset($data['event']) && !empty(array_filter($data['event']))) {
                    $record->createOrUpdateEvent($data['event']);
                }
                break;
                
            case 'PRODUCT':
                if (isset($data['product']) && !empty(array_filter($data['product']))) {
                    $record->createOrUpdateProduct($data['product']);
                }
                break;
                
            case 'TOURIST':
                if (isset($data['tourist']) && !empty(array_filter($data['tourist']))) {
                    $record->createOrUpdateTourist($data['tourist']);
                }
                break;
        }
        
        // Guardar social links si es perfil
        if ($record->type === 'PROFILE' && isset($data['socialLinks'])) {
            foreach ($data['socialLinks'] as $link) {
                if (!empty(array_filter($link))) {
                    $record->socialLinks()->create($link);
                }
            }
        }
        
        // Guardar skills si es perfil
        if ($record->type === 'PROFILE' && isset($data['skills'])) {
            foreach ($data['skills'] as $skill) {
                if (!empty(array_filter($skill))) {
                    $record->skills()->create($skill);
                }
            }
        }
    }
}
