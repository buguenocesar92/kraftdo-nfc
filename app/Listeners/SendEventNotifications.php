<?php

namespace App\Listeners;

use App\Events\ContentEventCreated;
use App\Services\ContentObservabilityService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendEventNotifications implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(ContentEventCreated $event): void
    {
        $contentEvent = $event->contentEvent;
        $metadata = $event->metadata;

        Log::info('Processing event notifications', [
            'event_id' => $contentEvent->id,
            'user_id' => $metadata['user_id'],
        ]);

        // Send notifications based on event characteristics
        $this->processEventNotifications($contentEvent, $metadata);

        // Log analytics
        ContentObservabilityService::logUserBehavior('event_created', [
            'event_id' => $contentEvent->id,
            'has_future_date' => $contentEvent->event_start_date && $contentEvent->event_start_date->isFuture(),
            'has_location' => !empty($contentEvent->event_location),
            'has_price' => !empty($contentEvent->ticket_price),
        ]);
    }

    /**
     * Process different types of notifications for the event
     */
    private function processEventNotifications($contentEvent, array $metadata): void
    {
        // 1. Notify event organizer
        $this->notifyEventOrganizer($contentEvent, $metadata);

        // 2. If event is public and in the future, add to public calendar
        if ($this->isPublicEvent($contentEvent) && $this->isFutureEvent($contentEvent)) {
            $this->addToPublicCalendar($contentEvent);
        }

        // 3. If event has registration, prepare registration tracking
        if (!empty($contentEvent->registration_url)) {
            $this->setupRegistrationTracking($contentEvent);
        }

        // 4. If event is within next 30 days, add to promotional queue
        if ($this->isUpcomingEvent($contentEvent)) {
            $this->addToPromotionalQueue($contentEvent);
        }
    }

    /**
     * Notify the event organizer about successful creation
     */
    private function notifyEventOrganizer($contentEvent, array $metadata): void
    {
        if ($user = \App\Models\User::find($metadata['user_id'])) {
            // In a real implementation, you'd send email/SMS here
            Log::info('Event organizer notification sent', [
                'event_id' => $contentEvent->id,
                'organizer_email' => $user->email,
                'event_location' => $contentEvent->event_location,
            ]);

            // Example: Send email notification
            // Mail::to($user->email)->send(new EventCreatedNotification($contentEvent));
        }
    }

    /**
     * Add event to public calendar system
     */
    private function addToPublicCalendar($contentEvent): void
    {
        Log::info('Adding event to public calendar', [
            'event_id' => $contentEvent->id,
            'start_date' => $contentEvent->event_start_date,
            'location' => $contentEvent->event_location,
        ]);

        // Integration with calendar service would go here
        // CalendarService::addPublicEvent($contentEvent);
    }

    /**
     * Setup registration tracking for events with registration URLs
     */
    private function setupRegistrationTracking($contentEvent): void
    {
        Log::info('Setting up registration tracking', [
            'event_id' => $contentEvent->id,
            'registration_url' => $contentEvent->registration_url,
        ]);

        // Integration with analytics/tracking service
        // AnalyticsService::trackEventRegistrations($contentEvent);
    }

    /**
     * Add event to promotional queue for marketing
     */
    private function addToPromotionalQueue($contentEvent): void
    {
        Log::info('Adding event to promotional queue', [
            'event_id' => $contentEvent->id,
            'days_until_event' => $contentEvent->event_start_date ? 
                now()->diffInDays($contentEvent->event_start_date) : null,
        ]);

        // Integration with marketing automation
        // MarketingService::queueEventPromotion($contentEvent);
    }

    /**
     * Check if event is public
     */
    private function isPublicEvent($contentEvent): bool
    {
        return $contentEvent->dynamicContent && 
               $contentEvent->dynamicContent->status === 'published' &&
               $contentEvent->dynamicContent->is_active;
    }

    /**
     * Check if event is in the future
     */
    private function isFutureEvent($contentEvent): bool
    {
        return $contentEvent->event_start_date && 
               $contentEvent->event_start_date->isFuture();
    }

    /**
     * Check if event is within next 30 days
     */
    private function isUpcomingEvent($contentEvent): bool
    {
        if (!$contentEvent->event_start_date) {
            return false;
        }

        $daysUntilEvent = now()->diffInDays($contentEvent->event_start_date, false);
        return $daysUntilEvent >= 0 && $daysUntilEvent <= 30;
    }

    /**
     * Handle a job failure.
     */
    public function failed(ContentEventCreated $event, \Throwable $exception): void
    {
        Log::error('Event notification job failed', [
            'event_id' => $event->contentEvent->id,
            'error' => $exception->getMessage(),
            'stack_trace' => $exception->getTraceAsString(),
        ]);

        // Could implement retry logic or alert administrators
        ContentObservabilityService::logSecurityEvent('notification_failure', [
            'event_id' => $event->contentEvent->id,
            'error_type' => get_class($exception),
        ]);
    }
}