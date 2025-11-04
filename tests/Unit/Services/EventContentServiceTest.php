<?php

use App\Models\DynamicContent;
use App\Models\ContentEvent;
use App\Models\User;
use App\Services\EventContentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

describe('EventContentService', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->service = app(EventContentService::class);
    });

    describe('createEventContent', function () {
        test('creates event content successfully', function () {
            Auth::login($this->user);
            
            $dynamicContent = DynamicContent::factory()->create([
                'user_id' => $this->user->id,
                'type' => 'EVENT'
            ]);

            $data = [
                'event_location' => 'Santiago Convention Center',
                'event_start_date' => '2024-12-01 10:00:00',
                'event_end_date' => '2024-12-01 18:00:00',
                'event_organizer' => 'Tech Events Chile',
                'registration_url' => 'https://event.com/register',
                'ticket_price' => 50000,
                'currency' => 'CLP'
            ];

            $result = $this->service->createEventContent($dynamicContent->id, $data);

            expect($result)->toBeInstanceOf(ContentEvent::class)
                ->and($result->dynamic_content_id)->toBe($dynamicContent->id)
                ->and($result->event_location)->toBe('Santiago Convention Center')
                ->and($result->event_start_date->format('Y-m-d H:i:s'))->toBe('2024-12-01 10:00:00')
                ->and($result->event_organizer)->toBe('Tech Events Chile')
                ->and($result->registration_url)->toBe('https://event.com/register')
                ->and($result->ticket_price)->toEqual(50000.00)
                ->and($result->ticket_currency)->toBe('CLP');
        });

        test('throws exception for non-existent dynamic content', function () {
            Auth::login($this->user);

            $this->service->createEventContent(99999, [
                'event_location' => 'Test Location'
            ]);
        })->throws(ModelNotFoundException::class);

        test('throws exception for unauthorized dynamic content', function () {
            Auth::login($this->user);
            
            $otherUser = User::factory()->create();
            $dynamicContent = DynamicContent::factory()->create([
                'user_id' => $otherUser->id,
                'type' => 'EVENT'
            ]);

            $this->service->createEventContent($dynamicContent->id, [
                'event_location' => 'Test Location'
            ]);
        })->throws(ModelNotFoundException::class);

        test('creates event with minimal data', function () {
            Auth::login($this->user);
            
            $dynamicContent = DynamicContent::factory()->create([
                'user_id' => $this->user->id,
                'type' => 'EVENT'
            ]);

            $result = $this->service->createEventContent($dynamicContent->id, []);

            expect($result)->toBeInstanceOf(ContentEvent::class)
                ->and($result->dynamic_content_id)->toBe($dynamicContent->id)
                ->and($result->event_location)->toBeNull();
        });
    });

    describe('getEventContent', function () {
        test('gets event content successfully', function () {
            Auth::login($this->user);
            
            $dynamicContent = DynamicContent::factory()->create([
                'user_id' => $this->user->id,
                'type' => 'EVENT'
            ]);

            $eventContent = ContentEvent::create([
                'dynamic_content_id' => $dynamicContent->id,
                'event_location' => 'Test Location'
            ]);

            $result = $this->service->getEventContent($dynamicContent->id);

            expect($result)->toBeInstanceOf(ContentEvent::class)
                ->and($result->id)->toBe($eventContent->id)
                ->and($result->event_location)->toBe('Test Location');
        });

        test('throws exception for non-existent dynamic content', function () {
            Auth::login($this->user);

            $this->service->getEventContent(99999);
        })->throws(ModelNotFoundException::class);

        test('throws exception for unauthorized dynamic content', function () {
            Auth::login($this->user);
            
            $otherUser = User::factory()->create();
            $dynamicContent = DynamicContent::factory()->create([
                'user_id' => $otherUser->id,
                'type' => 'EVENT'
            ]);

            $this->service->getEventContent($dynamicContent->id);
        })->throws(ModelNotFoundException::class);

        test('throws exception when event content does not exist', function () {
            Auth::login($this->user);
            
            $dynamicContent = DynamicContent::factory()->create([
                'user_id' => $this->user->id,
                'type' => 'EVENT'
            ]);

            $this->service->getEventContent($dynamicContent->id);
        })->throws(ModelNotFoundException::class);
    });

    describe('updateEventContent', function () {
        test('updates event content successfully', function () {
            Auth::login($this->user);
            
            $dynamicContent = DynamicContent::factory()->create([
                'user_id' => $this->user->id,
                'type' => 'EVENT'
            ]);

            $eventContent = ContentEvent::create([
                'dynamic_content_id' => $dynamicContent->id,
                'event_location' => 'Old Location'
            ]);

            $updateData = [
                'event_location' => 'New Location',
                'ticket_price' => 75000
            ];

            $result = $this->service->updateEventContent($eventContent->id, $updateData);

            expect($result)->toBeInstanceOf(ContentEvent::class)
                ->and($result->id)->toBe($eventContent->id)
                ->and($result->event_location)->toBe('New Location')
                ->and($result->ticket_price)->toEqual(75000.00);
        });

        test('throws exception for non-existent event content', function () {
            Auth::login($this->user);

            $this->service->updateEventContent(99999, [
                'event_location' => 'New Location'
            ]);
        })->throws(ModelNotFoundException::class);

        test('throws exception for unauthorized event content', function () {
            Auth::login($this->user);
            
            $otherUser = User::factory()->create();
            $dynamicContent = DynamicContent::factory()->create([
                'user_id' => $otherUser->id,
                'type' => 'EVENT'
            ]);

            $eventContent = ContentEvent::create([
                'dynamic_content_id' => $dynamicContent->id
            ]);

            $this->service->updateEventContent($eventContent->id, [
                'event_location' => 'New Location'
            ]);
        })->throws(ModelNotFoundException::class);

        test('updates only provided fields', function () {
            Auth::login($this->user);
            
            $dynamicContent = DynamicContent::factory()->create([
                'user_id' => $this->user->id,
                'type' => 'EVENT'
            ]);

            $eventContent = ContentEvent::create([
                'dynamic_content_id' => $dynamicContent->id,
                'event_location' => 'Original Location',
                'event_organizer' => 'Original Organizer'
            ]);

            $result = $this->service->updateEventContent($eventContent->id, [
                'ticket_price' => 200
            ]);

            expect($result->event_location)->toBe('Original Location')
                ->and($result->ticket_price)->toEqual(200.00)
                ->and($result->event_organizer)->toBe('Original Organizer');
        });
    });

    describe('deleteEventContent', function () {
        test('deletes event content successfully', function () {
            Auth::login($this->user);
            
            $dynamicContent = DynamicContent::factory()->create([
                'user_id' => $this->user->id,
                'type' => 'EVENT'
            ]);

            $eventContent = ContentEvent::create([
                'dynamic_content_id' => $dynamicContent->id
            ]);

            $result = $this->service->deleteEventContent($eventContent->id);

            expect($result)->toBeTrue();
            
            // Verify deletion
            expect(ContentEvent::find($eventContent->id))->toBeNull();
        });

        test('throws exception for non-existent event content', function () {
            Auth::login($this->user);

            $this->service->deleteEventContent(99999);
        })->throws(ModelNotFoundException::class);

        test('throws exception for unauthorized event content', function () {
            Auth::login($this->user);
            
            $otherUser = User::factory()->create();
            $dynamicContent = DynamicContent::factory()->create([
                'user_id' => $otherUser->id,
                'type' => 'EVENT'
            ]);

            $eventContent = ContentEvent::create([
                'dynamic_content_id' => $dynamicContent->id
            ]);

            $this->service->deleteEventContent($eventContent->id);
        })->throws(ModelNotFoundException::class);
    });

    describe('error handling', function () {
        test('handles database transaction rollback on create failure', function () {
            Auth::login($this->user);
            
            $dynamicContent = DynamicContent::factory()->create([
                'user_id' => $this->user->id,
                'type' => 'EVENT'
            ]);

            // Test with invalid date format that might cause DB error
            expect(fn() => $this->service->createEventContent($dynamicContent->id, [
                'event_start_date' => 'invalid-date'
            ]))->toThrow(Exception::class);
        });

        test('handles unauthenticated user gracefully', function () {
            // No Auth::login() call
            
            $dynamicContent = DynamicContent::factory()->create([
                'user_id' => $this->user->id,
                'type' => 'EVENT'
            ]);

            $this->service->createEventContent($dynamicContent->id, []);
        })->throws(ModelNotFoundException::class);
    });

    describe('data validation and business logic', function () {
        test('handles different currencies', function () {
            Auth::login($this->user);
            
            $dynamicContent = DynamicContent::factory()->create([
                'user_id' => $this->user->id,
                'type' => 'EVENT'
            ]);

            $currencies = ['CLP', 'USD', 'EUR'];
            
            foreach ($currencies as $currency) {
                $result = $this->service->createEventContent($dynamicContent->id, [
                    'currency' => $currency,
                    'ticket_price' => 100
                ]);
                
                expect($result->ticket_currency)->toBe($currency);
                
                // Clean up for next iteration
                $result->delete();
            }
        });

        test('handles different organizers', function () {
            Auth::login($this->user);
            
            $dynamicContent = DynamicContent::factory()->create([
                'user_id' => $this->user->id,
                'type' => 'EVENT'
            ]);

            $organizers = ['Tech Events Chile', 'Fundación Laravel', 'Universidad de Chile'];
            
            foreach ($organizers as $organizer) {
                $result = $this->service->createEventContent($dynamicContent->id, [
                    'event_organizer' => $organizer
                ]);
                
                expect($result->event_organizer)->toBe($organizer);
                
                // Clean up for next iteration
                $result->delete();
            }
        });
    });
});