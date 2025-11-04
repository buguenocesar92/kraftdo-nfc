<?php

namespace App\Providers;

use App\Events\ContentEventCreated;
use App\Listeners\SendEventNotifications;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        
        // Content Events
        ContentEventCreated::class => [
            SendEventNotifications::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        parent::boot();

        // Register additional event listeners programmatically
        Event::listen(
            'content.*.created',
            function (string $eventName, array $data) {
                // Generic handler for any content creation event
                \Illuminate\Support\Facades\Log::info('Content created via wildcard listener', [
                    'event' => $eventName,
                    'data_keys' => array_keys($data),
                ]);
            }
        );

        Event::listen(
            'content.*.updated',
            function (string $eventName, array $data) {
                // Generic handler for content updates
                \Illuminate\Support\Facades\Log::info('Content updated via wildcard listener', [
                    'event' => $eventName,
                    'data_keys' => array_keys($data),
                ]);
            }
        );
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}