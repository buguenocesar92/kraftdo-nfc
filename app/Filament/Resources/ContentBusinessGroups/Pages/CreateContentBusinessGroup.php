<?php

namespace App\Filament\Resources\ContentBusinessGroups\Pages;

use App\Filament\Resources\ContentBusinessGroups\ContentBusinessGroupResource;
use Filament\Resources\Pages\CreateRecord;

class CreateContentBusinessGroup extends CreateRecord
{
    protected static string $resource = ContentBusinessGroupResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
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
