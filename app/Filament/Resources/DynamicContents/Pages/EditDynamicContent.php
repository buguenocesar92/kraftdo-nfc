<?php

namespace App\Filament\Resources\DynamicContents\Pages;

use App\Filament\Resources\DynamicContents\DynamicContentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDynamicContent extends EditRecord
{
    protected static string $resource = DynamicContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Cargar datos de relaciones normalizadas
        $record = $this->record;
        
        // Multimedia
        if ($record->multimedia) {
            $data['multimedia'] = [
                'video_url' => $record->multimedia->video_url,
                'video_type' => $record->multimedia->video_type,
                'audio_url' => $record->multimedia->audio_url,
                'audio_type' => $record->multimedia->audio_type,
                'gallery_images' => $record->multimedia->gallery_images ?? [],
            ];
        }
        
        // Gift
        if ($record->gift && $record->type === 'GIFT') {
            $data['gift'] = [
                'sender_name' => $record->gift->sender_name,
                'recipient_name' => $record->gift->recipient_name,
                'message' => $record->gift->message,
            ];
        }
        
        // Menu
        if ($record->menu && $record->type === 'MENU') {
            $data['menu'] = [
                'restaurant_name' => $record->menu->restaurant_name,
                'restaurant_phone' => $record->menu->restaurant_phone,
                'restaurant_address' => $record->menu->restaurant_address,
                'restaurant_hours' => $record->menu->restaurant_hours,
                'menu_items' => $record->menu->menu_items ?? [],
            ];
        }
        
        // Profile
        if ($record->profile && $record->type === 'PROFILE') {
            $data['profile'] = [
                'contact_email' => $record->profile->contact_email,
                'contact_phone' => $record->profile->contact_phone,
                'contact_website' => $record->profile->contact_website,
                'bio' => $record->profile->bio,
            ];
        }
        
        // Event
        if ($record->event && $record->type === 'EVENT') {
            $data['event'] = [
                'event_location' => $record->event->event_location,
                'event_start_date' => $record->event->event_start_date,
                'event_end_date' => $record->event->event_end_date,
                'event_organizer' => $record->event->event_organizer,
                'event_description' => $record->event->event_description,
                'event_capacity' => $record->event->event_capacity,
                'registration_required' => $record->event->registration_required,
                'registration_url' => $record->event->registration_url,
                'ticket_price' => $record->event->ticket_price,
            ];
        }
        
        // Product
        if ($record->product && $record->type === 'PRODUCT') {
            $data['product'] = [
                'product_price' => $record->product->product_price,
                'product_currency' => $record->product->product_currency,
                'product_sku' => $record->product->product_sku,
                'product_stock' => $record->product->product_stock,
                'product_description' => $record->product->product_description,
                'product_weight' => $record->product->product_weight,
                'availability_status' => $record->product->availability_status,
            ];
        }
        
        // Tourist
        if ($record->tourist && $record->type === 'TOURIST') {
            $data['tourist'] = [
                'location_name' => $record->tourist->location_name,
                'location_address' => $record->tourist->location_address,
                'contact_phone' => $record->tourist->contact_phone,
                'contact_email' => $record->tourist->contact_email,
                'website_url' => $record->tourist->website_url,
                'description' => $record->tourist->description,
                'best_time_to_visit' => $record->tourist->best_time_to_visit,
                'attractions' => $record->tourist->attractions ?? [],
            ];
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Los datos de relaciones se manejan en afterSave
        return $data;
    }

    protected function afterSave(): void
    {
        $data = $this->form->getState();
        $record = $this->record;
        
        // Guardar multimedia - solo si no se seleccionó uno existente y hay nuevos datos
        if (!$data['multimedia_id'] && isset($data['multimedia']) && !empty(array_filter($data['multimedia']))) {
            $record->createOrUpdateMultimedia($data['multimedia']);
        }
        
        // Guardar según tipo - solo si no se seleccionó uno existente y hay nuevos datos
        switch ($record->type) {
            case 'GIFT':
                if (!$data['gift_id'] && isset($data['gift']) && !empty(array_filter($data['gift']))) {
                    $record->createOrUpdateGift($data['gift']);
                }
                break;
                
            case 'MENU':
                if (!$data['menu_id'] && isset($data['menu']) && !empty(array_filter($data['menu']))) {
                    $record->createOrUpdateMenu($data['menu']);
                }
                break;
                
            case 'PROFILE':
                if (!$data['profile_id'] && isset($data['profile']) && !empty(array_filter($data['profile']))) {
                    $record->createOrUpdateProfile($data['profile']);
                }
                break;
                
            case 'EVENT':
                if (!$data['event_id'] && isset($data['event']) && !empty(array_filter($data['event']))) {
                    $record->createOrUpdateEvent($data['event']);
                }
                break;
                
            case 'PRODUCT':
                if (!$data['product_id'] && isset($data['product']) && !empty(array_filter($data['product']))) {
                    $record->createOrUpdateProduct($data['product']);
                }
                break;
                
            case 'TOURIST':
                if (!$data['tourist_id'] && isset($data['tourist']) && !empty(array_filter($data['tourist']))) {
                    $record->createOrUpdateTourist($data['tourist']);
                }
                break;
        }
    }
}
