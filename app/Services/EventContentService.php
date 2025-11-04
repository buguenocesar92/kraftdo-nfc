<?php

namespace App\Services;

use App\Models\ContentEvent;
use App\Models\DynamicContent;
use App\Services\ContentObservabilityService;
use App\Events\ContentEventCreated;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EventContentService
{
    /**
     * Create a new event content
     */
    public function createEventContent(int $dynamicContentId, array $data): ContentEvent
    {
        return DB::transaction(function () use ($dynamicContentId, $data) {
            // Verify user owns the dynamic content
            $dynamicContent = DynamicContent::where('id', $dynamicContentId)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            // Create event content
            $eventContent = ContentEvent::create([
                'dynamic_content_id' => $dynamicContentId,
                'event_location' => $data['event_location'] ?? null,
                'event_start_date' => $data['event_start_date'] ?? null,
                'event_end_date' => $data['event_end_date'] ?? null,
                'event_organizer' => $data['event_organizer'] ?? null,
                'ticket_price' => $data['ticket_price'] ?? null,
                'ticket_currency' => $data['ticket_currency'] ?? $data['currency'] ?? 'USD',
                'registration_url' => $data['registration_url'] ?? null,
            ]);

            // Log event creation
            ContentObservabilityService::logContentCreation('EVENT', $eventContent->id, $data);

            // Dispatch event for further processing
            ContentEventCreated::dispatch($eventContent, [
                'source' => 'api',
                'original_data' => $data,
            ]);

            return $eventContent;
        });
    }

    /**
     * Get event content by dynamic content ID
     */
    public function getEventContent(int $dynamicContentId): ContentEvent
    {
        // Verify user owns the dynamic content
        $dynamicContent = DynamicContent::where('id', $dynamicContentId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return ContentEvent::where('dynamic_content_id', $dynamicContentId)
            ->with('dynamicContent')
            ->firstOrFail();
    }

    /**
     * Update event content
     */
    public function updateEventContent(int $eventId, array $data): ContentEvent
    {
        $eventContent = ContentEvent::whereHas('dynamicContent', function ($query) {
            $query->where('user_id', Auth::id());
        })->findOrFail($eventId);

        if (!empty($data)) {
            $eventContent->update($data);
        }

        return $eventContent->fresh();
    }

    /**
     * Delete event content
     */
    public function deleteEventContent(int $eventId): bool
    {
        $eventContent = ContentEvent::whereHas('dynamicContent', function ($query) {
            $query->where('user_id', Auth::id());
        })->findOrFail($eventId);

        return $eventContent->delete();
    }

    /**
     * Check if user owns the event content
     */
    public function userOwnsEventContent(int $eventId): bool
    {
        return ContentEvent::whereHas('dynamicContent', function ($query) {
            $query->where('user_id', Auth::id());
        })->where('id', $eventId)->exists();
    }
}