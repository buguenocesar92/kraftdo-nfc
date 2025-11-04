<?php

namespace App\Events;

use App\Models\ContentEvent;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ContentEventCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ContentEvent $contentEvent;
    public array $metadata;

    /**
     * Create a new event instance.
     */
    public function __construct(ContentEvent $contentEvent, array $metadata = [])
    {
        $this->contentEvent = $contentEvent;
        $this->metadata = array_merge([
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
            'created_at' => now()->toISOString(),
        ], $metadata);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->metadata['user_id']),
            new Channel('events.public'), // For public events
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'event_id' => $this->contentEvent->id,
            'event_type' => 'content.event.created',
            'event_data' => [
                'location' => $this->contentEvent->event_location,
                'start_date' => $this->contentEvent->event_start_date,
                'organizer' => $this->contentEvent->event_organizer,
                'has_registration' => !empty($this->contentEvent->registration_url),
            ],
            'metadata' => $this->metadata,
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'content.event.created';
    }
}