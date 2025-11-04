<?php

namespace App\Policies;

use App\Models\ContentEvent;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ContentEventPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Allow authenticated users to view their own events
        return $user !== null;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ContentEvent $contentEvent): bool
    {
        // Users can view events they own or public events
        return $this->owns($user, $contentEvent) || $this->isPublicEvent($contentEvent);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Allow authenticated users to create events
        return $user !== null && $user->hasVerifiedEmail();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ContentEvent $contentEvent): bool
    {
        // Only owners can update their events
        return $this->owns($user, $contentEvent) && $this->canModifyEvent($contentEvent);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ContentEvent $contentEvent): bool
    {
        // Only owners can delete their events, and only if not started
        return $this->owns($user, $contentEvent) && !$this->hasEventStarted($contentEvent);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ContentEvent $contentEvent): bool
    {
        // Only owners can restore their events
        return $this->owns($user, $contentEvent);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ContentEvent $contentEvent): bool
    {
        // Only admins can force delete events
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can manage event registrations.
     */
    public function manageRegistrations(User $user, ContentEvent $contentEvent): bool
    {
        // Only event owners can manage registrations
        return $this->owns($user, $contentEvent);
    }

    /**
     * Determine whether the user can publish/unpublish the event.
     */
    public function publish(User $user, ContentEvent $contentEvent): bool
    {
        // Only owners can publish their events
        return $this->owns($user, $contentEvent) && $this->isValidForPublication($contentEvent);
    }

    /**
     * Check if user owns the event content.
     */
    private function owns(User $user, ContentEvent $contentEvent): bool
    {
        return $contentEvent->dynamicContent?->user_id === $user->id;
    }

    /**
     * Check if event is public (published and active).
     */
    private function isPublicEvent(ContentEvent $contentEvent): bool
    {
        return $contentEvent->dynamicContent?->status === 'published' &&
               $contentEvent->dynamicContent?->is_active === true;
    }

    /**
     * Check if event can be modified (not started yet).
     */
    private function canModifyEvent(ContentEvent $contentEvent): bool
    {
        return !$this->hasEventStarted($contentEvent);
    }

    /**
     * Check if event has already started.
     */
    private function hasEventStarted(ContentEvent $contentEvent): bool
    {
        return $contentEvent->event_start_date && 
               $contentEvent->event_start_date->isPast();
    }

    /**
     * Check if event is valid for publication.
     */
    private function isValidForPublication(ContentEvent $contentEvent): bool
    {
        return !empty($contentEvent->event_location) &&
               !empty($contentEvent->event_start_date) &&
               !empty($contentEvent->event_organizer);
    }
}
