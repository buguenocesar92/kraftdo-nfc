<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventContentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'dynamic_content_id' => $this->dynamic_content_id,
            'event_location' => $this->event_location,
            'event_start_date' => $this->event_start_date?->toISOString(),
            'event_end_date' => $this->event_end_date?->toISOString(),
            'event_organizer' => $this->event_organizer,
            'ticket_price' => $this->ticket_price,
            'ticket_currency' => $this->ticket_currency,
            'registration_url' => $this->registration_url,
            'is_active' => $this->isActive(),
            'requires_registration' => $this->requiresRegistration(),
            'duration_hours' => $this->getDurationInHours(),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'dynamic_content' => new DynamicContentResource($this->whenLoaded('dynamicContent')),
        ];
    }
}