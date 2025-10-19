<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BusStopContentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'stop_id' => $this->stop_id,
            'name' => $this->name,
            'address' => $this->address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'municipality_name' => $this->municipality_name,
            'municipality_website' => $this->municipality_website,
            'municipality_description' => $this->municipality_description,
            'routes' => $this->when($this->relationLoaded('routes'), function () {
                return $this->routes;
            }),
            'utility_phones' => $this->when($this->relationLoaded('utilityPhones'), function () {
                return $this->utilityPhones;
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
