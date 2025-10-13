<?php

namespace App\Filament\Resources\ContentBusinessGroups\Pages;

use App\Filament\Resources\ContentBusinessGroups\ContentBusinessGroupResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditContentBusinessGroup extends EditRecord
{
    protected static string $resource = ContentBusinessGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Combine latitude and longitude into location_coordinates
        if (isset($data['latitude']) && isset($data['longitude'])) {
            $data['location_coordinates'] = [
                'lat' => (float) $data['latitude'],
                'lng' => (float) $data['longitude'],
            ];
        }
        
        // Remove the separate fields
        unset($data['latitude'], $data['longitude']);
        
        return $data;
    }
}
