<?php

use App\Models\DynamicContent;
use App\Models\ContentTourist;
use App\Models\User;
use App\Services\TouristContentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

describe('TouristContentService', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->service = app(TouristContentService::class);
    });

    describe('createTouristContent', function () {
        test('creates tourist content successfully', function () {
            Auth::login($this->user);
            
            $dynamicContent = DynamicContent::factory()->create([
                'user_id' => $this->user->id,
                'type' => 'TOURIST'
            ]);

            $data = [
                'location_name' => 'Plaza de Armas Santiago',
                'place_type' => 'HISTORIC',
                'location_address' => 'Plaza de Armas, Santiago, Chile',
                'latitude' => -33.4382,
                'longitude' => -70.6508,
                'history' => 'Historic main square of Santiago',
                'opening_hours' => '24/7',
                'pricing_info' => 'Free entry',
                'contact_phone' => '+56912345678',
                'contact_email' => 'info@plazadearmas.cl',
                'website_url' => 'https://santiago.cl/plaza-armas',
                'accessibility_info' => 'Wheelchair accessible',
                'best_time_to_visit' => 'Morning hours',
                'languages_spoken' => 'Spanish,English',
                'services' => 'Restrooms,Parking,WiFi'
            ];

            $result = $this->service->createTouristContent($dynamicContent->id, $data);

            expect($result)->toBeInstanceOf(ContentTourist::class)
                ->and($result->dynamic_content_id)->toBe($dynamicContent->id)
                ->and($result->location_name)->toBe('Plaza de Armas Santiago')
                ->and($result->place_type)->toBe('HISTORIC')
                ->and($result->latitude)->toEqual(-33.4382)
                ->and($result->longitude)->toEqual(-70.6508)
                ->and($result->history)->toBe('Historic main square of Santiago');
        });

        test('throws exception for non-existent dynamic content', function () {
            Auth::login($this->user);

            $this->service->createTouristContent(99999, [
                'location_name' => 'Test Attraction'
            ]);
        })->throws(ModelNotFoundException::class);

        test('throws exception for unauthorized dynamic content', function () {
            Auth::login($this->user);
            
            $otherUser = User::factory()->create();
            $dynamicContent = DynamicContent::factory()->create([
                'user_id' => $otherUser->id,
                'type' => 'TOURIST'
            ]);

            $this->service->createTouristContent($dynamicContent->id, [
                'location_name' => 'Test Attraction'
            ]);
        })->throws(ModelNotFoundException::class);

        test('creates tourist content with minimal data', function () {
            Auth::login($this->user);
            
            $dynamicContent = DynamicContent::factory()->create([
                'user_id' => $this->user->id,
                'type' => 'TOURIST'
            ]);

            $result = $this->service->createTouristContent($dynamicContent->id, [
                'location_name' => 'Test Location'
            ]);

            expect($result)->toBeInstanceOf(ContentTourist::class)
                ->and($result->dynamic_content_id)->toBe($dynamicContent->id)
                ->and($result->location_name)->toBe('Test Location')
                ->and($result->place_type)->toBe('recreativo');
        });
    });

    describe('getTouristContent', function () {
        test('gets tourist content successfully', function () {
            Auth::login($this->user);
            
            $dynamicContent = DynamicContent::factory()->create([
                'user_id' => $this->user->id,
                'type' => 'TOURIST'
            ]);

            $touristContent = ContentTourist::create([
                'dynamic_content_id' => $dynamicContent->id,
                'location_name' => 'Test Attraction'
            ]);

            $result = $this->service->getTouristContent($dynamicContent->id);

            expect($result)->toBeInstanceOf(ContentTourist::class)
                ->and($result->id)->toBe($touristContent->id)
                ->and($result->location_name)->toBe('Test Attraction');
        });

        test('throws exception for non-existent dynamic content', function () {
            Auth::login($this->user);

            $this->service->getTouristContent(99999);
        })->throws(ModelNotFoundException::class);

        test('throws exception for unauthorized dynamic content', function () {
            Auth::login($this->user);
            
            $otherUser = User::factory()->create();
            $dynamicContent = DynamicContent::factory()->create([
                'user_id' => $otherUser->id,
                'type' => 'TOURIST'
            ]);

            $this->service->getTouristContent($dynamicContent->id);
        })->throws(ModelNotFoundException::class);

        test('throws exception when tourist content does not exist', function () {
            Auth::login($this->user);
            
            $dynamicContent = DynamicContent::factory()->create([
                'user_id' => $this->user->id,
                'type' => 'TOURIST'
            ]);

            $this->service->getTouristContent($dynamicContent->id);
        })->throws(ModelNotFoundException::class);
    });

    describe('updateTouristContent', function () {
        test('updates tourist content successfully', function () {
            Auth::login($this->user);
            
            $dynamicContent = DynamicContent::factory()->create([
                'user_id' => $this->user->id,
                'type' => 'TOURIST'
            ]);

            $touristContent = ContentTourist::create([
                'dynamic_content_id' => $dynamicContent->id,
                'location_name' => 'Old Attraction',
                'pricing_info' => '1000 CLP'
            ]);

            $updateData = [
                'location_name' => 'New Attraction',
                'pricing_info' => '2000 CLP'
            ];

            $result = $this->service->updateTouristContent($touristContent->id, $updateData);

            expect($result)->toBeInstanceOf(ContentTourist::class)
                ->and($result->id)->toBe($touristContent->id)
                ->and($result->location_name)->toBe('New Attraction')
                ->and($result->pricing_info)->toBe('2000 CLP');
        });

        test('throws exception for non-existent tourist content', function () {
            Auth::login($this->user);

            $this->service->updateTouristContent(99999, [
                'location_name' => 'New Attraction'
            ]);
        })->throws(ModelNotFoundException::class);

        test('throws exception for unauthorized tourist content', function () {
            Auth::login($this->user);
            
            $otherUser = User::factory()->create();
            $dynamicContent = DynamicContent::factory()->create([
                'user_id' => $otherUser->id,
                'type' => 'TOURIST'
            ]);

            $touristContent = ContentTourist::create([
                'dynamic_content_id' => $dynamicContent->id,
                'location_name' => 'Test Location'
            ]);

            $this->service->updateTouristContent($touristContent->id, [
                'location_name' => 'New Attraction'
            ]);
        })->throws(ModelNotFoundException::class);

        test('updates only provided fields', function () {
            Auth::login($this->user);
            
            $dynamicContent = DynamicContent::factory()->create([
                'user_id' => $this->user->id,
                'type' => 'TOURIST'
            ]);

            $touristContent = ContentTourist::create([
                'dynamic_content_id' => $dynamicContent->id,
                'location_name' => 'Original Attraction',
                'pricing_info' => '1000 CLP'
            ]);

            $result = $this->service->updateTouristContent($touristContent->id, [
                'pricing_info' => '1500 CLP'
            ]);

            expect($result->location_name)->toBe('Original Attraction')
                ->and($result->pricing_info)->toBe('1500 CLP');
        });
    });

    describe('deleteTouristContent', function () {
        test('deletes tourist content successfully', function () {
            Auth::login($this->user);
            
            $dynamicContent = DynamicContent::factory()->create([
                'user_id' => $this->user->id,
                'type' => 'TOURIST'
            ]);

            $touristContent = ContentTourist::create([
                'dynamic_content_id' => $dynamicContent->id,
                'location_name' => 'Test Location'
            ]);

            $result = $this->service->deleteTouristContent($touristContent->id);

            expect($result)->toBeTrue();
            
            // Verify deletion
            expect(ContentTourist::find($touristContent->id))->toBeNull();
        });

        test('throws exception for non-existent tourist content', function () {
            Auth::login($this->user);

            $this->service->deleteTouristContent(99999);
        })->throws(ModelNotFoundException::class);

        test('throws exception for unauthorized tourist content', function () {
            Auth::login($this->user);
            
            $otherUser = User::factory()->create();
            $dynamicContent = DynamicContent::factory()->create([
                'user_id' => $otherUser->id,
                'type' => 'TOURIST'
            ]);

            $touristContent = ContentTourist::create([
                'dynamic_content_id' => $dynamicContent->id,
                'location_name' => 'Test Location'
            ]);

            $this->service->deleteTouristContent($touristContent->id);
        })->throws(ModelNotFoundException::class);
    });

    describe('business logic and validation', function () {
        test('handles different tourist categories', function () {
            Auth::login($this->user);
            
            $dynamicContent = DynamicContent::factory()->create([
                'user_id' => $this->user->id,
                'type' => 'TOURIST'
            ]);

            $categories = ['HISTORIC', 'MUSEUM', 'NATURE', 'ADVENTURE', 'CULTURAL', 'RELIGIOUS', 'ENTERTAINMENT', 'OTHER'];
            
            foreach ($categories as $category) {
                $result = $this->service->createTouristContent($dynamicContent->id, [
                    'location_name' => 'Test Location',
                    'place_type' => $category
                ]);
                
                expect($result->place_type)->toBe($category);
                
                // Clean up for next iteration
                $result->delete();
            }
        });

        test('handles coordinate validation bounds', function () {
            Auth::login($this->user);
            
            $dynamicContent = DynamicContent::factory()->create([
                'user_id' => $this->user->id,
                'type' => 'TOURIST'
            ]);

            // Valid coordinates
            $validCoordinates = [
                ['latitude' => -33.4382, 'longitude' => -70.6508], // Santiago
                ['latitude' => 40.7128, 'longitude' => -74.0060], // NYC
                ['latitude' => 0, 'longitude' => 0], // Equator/Prime Meridian
                ['latitude' => -90, 'longitude' => -180], // South Pole boundary
                ['latitude' => 90, 'longitude' => 180], // North Pole boundary
            ];

            foreach ($validCoordinates as $coords) {
                $result = $this->service->createTouristContent($dynamicContent->id, array_merge([
                    'location_name' => 'Test Location'
                ], $coords));
                
                expect($result->latitude)->toEqual($coords['latitude'])
                    ->and($result->longitude)->toEqual($coords['longitude']);
                
                // Clean up for next iteration
                $result->delete();
            }
        });

        test('handles different pricing info formats', function () {
            Auth::login($this->user);
            
            $dynamicContent = DynamicContent::factory()->create([
                'user_id' => $this->user->id,
                'type' => 'TOURIST'
            ]);

            $pricingOptions = ['Free entry', '1000 CLP', '5 USD', 'Free for children under 10'];
            
            foreach ($pricingOptions as $pricing) {
                $result = $this->service->createTouristContent($dynamicContent->id, [
                    'location_name' => 'Test Location',
                    'pricing_info' => $pricing
                ]);
                
                expect($result->pricing_info)->toBe($pricing);
                
                // Clean up for next iteration
                $result->delete();
            }
        });

        test('handles different location addresses', function () {
            Auth::login($this->user);
            
            $dynamicContent = DynamicContent::factory()->create([
                'user_id' => $this->user->id,
                'type' => 'TOURIST'
            ]);

            $addresses = [
                'Plaza de Armas, Santiago, Chile',
                '123 Main St, New York, USA',
                'Av. Libertador, Buenos Aires, Argentina'
            ];
            
            foreach ($addresses as $address) {
                $result = $this->service->createTouristContent($dynamicContent->id, [
                    'location_name' => 'Test Location',
                    'location_address' => $address
                ]);
                
                expect($result->location_address)->toBe($address);
                
                // Clean up for next iteration
                $result->delete();
            }
        });

        test('handles multilingual data', function () {
            Auth::login($this->user);
            
            $dynamicContent = DynamicContent::factory()->create([
                'user_id' => $this->user->id,
                'type' => 'TOURIST'
            ]);

            $languageOptions = [
                'Spanish',
                'English',
                'Spanish,English',
                'Spanish,English,Portuguese',
                'French,German,Italian'
            ];
            
            foreach ($languageOptions as $languages) {
                $result = $this->service->createTouristContent($dynamicContent->id, [
                    'location_name' => 'Test Location',
                    'languages_spoken' => $languages
                ]);
                
                expect($result->languages_spoken)->toBe($languages);
                
                // Clean up for next iteration
                $result->delete();
            }
        });

        test('handles facility combinations', function () {
            Auth::login($this->user);
            
            $dynamicContent = DynamicContent::factory()->create([
                'user_id' => $this->user->id,
                'type' => 'TOURIST'
            ]);

            $facilityOptions = [
                'Restrooms',
                'Parking',
                'WiFi',
                'Restrooms,Parking',
                'Restrooms,Parking,WiFi,Restaurant,Gift Shop'
            ];
            
            foreach ($facilityOptions as $facilities) {
                $result = $this->service->createTouristContent($dynamicContent->id, [
                    'location_name' => 'Test Location',
                    'services' => $facilities
                ]);
                
                expect($result->services)->toBe($facilities);
                
                // Clean up for next iteration
                $result->delete();
            }
        });
    });

    describe('error handling', function () {
        test('handles database transaction rollback on create failure', function () {
            Auth::login($this->user);
            
            $dynamicContent = DynamicContent::factory()->create([
                'user_id' => $this->user->id,
                'type' => 'TOURIST'
            ]);

            // Test with invalid latitude that might cause DB error
            expect(fn() => $this->service->createTouristContent($dynamicContent->id, [
                'latitude' => 'invalid-coordinate'
            ]))->toThrow(Exception::class);
        });

        test('handles unauthenticated user gracefully', function () {
            // No Auth::login() call
            
            $dynamicContent = DynamicContent::factory()->create([
                'user_id' => $this->user->id,
                'type' => 'TOURIST'
            ]);

            $this->service->createTouristContent($dynamicContent->id, [
                'location_name' => 'Test Location'
            ]);
        })->throws(ModelNotFoundException::class);
    });
});