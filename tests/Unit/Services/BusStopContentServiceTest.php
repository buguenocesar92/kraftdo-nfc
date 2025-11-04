<?php

use App\Models\DynamicContent;
use App\Models\BusStop;
use App\Models\User;
use App\Services\BusStopContentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

describe('BusStopContentService', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->service = app(BusStopContentService::class);
    });

    describe('createBusStopContent', function () {
        test('creates bus stop content successfully', function () {
            Auth::login($this->user);
            
            $dynamicContent = DynamicContent::factory()->create([
                'user_id' => $this->user->id,
                'type' => 'BUS_STOP'
            ]);

            $data = [
                'stop_id' => 'BUS001',
                'name' => 'Plaza de Armas',
                'address' => 'Plaza de Armas 123, Santiago',
                'latitude' => -33.4382,
                'longitude' => -70.6508,
                'municipality_name' => 'Santiago',
                'municipality_website' => 'https://santiago.cl',
                'municipality_description' => 'Capital city of Chile'
            ];

            $result = $this->service->createBusStopContent($dynamicContent->id, $data);

            expect($result)->toBeInstanceOf(BusStop::class)
                ->and($result->dynamic_content_id)->toBe($dynamicContent->id)
                ->and($result->stop_id)->toBe('BUS001')
                ->and($result->name)->toBe('Plaza de Armas')
                ->and($result->address)->toBe('Plaza de Armas 123, Santiago')
                ->and($result->latitude)->toEqual(-33.4382)
                ->and($result->longitude)->toEqual(-70.6508)
                ->and($result->municipality_name)->toBe('Santiago')
                ->and($result->municipality_website)->toBe('https://santiago.cl');
        });

        test('throws exception for non-existent dynamic content', function () {
            Auth::login($this->user);

            $this->service->createBusStopContent(99999, [
                'stop_id' => 'BUS001',
                'name' => 'Test Stop'
            ]);
        })->throws(ModelNotFoundException::class);

        test('throws exception for unauthorized dynamic content', function () {
            Auth::login($this->user);
            
            $otherUser = User::factory()->create();
            $dynamicContent = DynamicContent::factory()->create([
                'user_id' => $otherUser->id,
                'type' => 'BUS_STOP'
            ]);

            $this->service->createBusStopContent($dynamicContent->id, [
                'stop_id' => 'BUS001',
                'name' => 'Test Stop'
            ]);
        })->throws(ModelNotFoundException::class);

        test('creates bus stop content with minimal data', function () {
            Auth::login($this->user);
            
            $dynamicContent = DynamicContent::factory()->create([
                'user_id' => $this->user->id,
                'type' => 'BUS_STOP'
            ]);

            $result = $this->service->createBusStopContent($dynamicContent->id, [
                'stop_id' => 'TEST001',
                'name' => 'Test Stop',
                'address' => 'Test Address',
                'latitude' => -33.4382,
                'longitude' => -70.6508,
                'municipality_name' => 'Test Municipality'
            ]);

            expect($result)->toBeInstanceOf(BusStop::class)
                ->and($result->dynamic_content_id)->toBe($dynamicContent->id)
                ->and($result->stop_id)->toBe('TEST001')
                ->and($result->name)->toBe('Test Stop');
        });
    });

    describe('getBusStopContent', function () {
        test('gets bus stop content successfully', function () {
            Auth::login($this->user);
            
            $dynamicContent = DynamicContent::factory()->create([
                'user_id' => $this->user->id,
                'type' => 'BUS_STOP'
            ]);

            $busStopContent = BusStop::create([
                'dynamic_content_id' => $dynamicContent->id,
                'stop_id' => 'BUS001',
                'name' => 'Test Bus Stop',
                'address' => 'Test Address',
                'latitude' => -33.4382,
                'longitude' => -70.6508,
                'municipality_name' => 'Test Municipality'
            ]);

            $result = $this->service->getBusStopContent($dynamicContent->id);

            expect($result)->toBeInstanceOf(BusStop::class)
                ->and($result->id)->toBe($busStopContent->id)
                ->and($result->stop_id)->toBe('BUS001')
                ->and($result->name)->toBe('Test Bus Stop');
        });

        test('throws exception for non-existent dynamic content', function () {
            Auth::login($this->user);

            $this->service->getBusStopContent(99999);
        })->throws(ModelNotFoundException::class);

        test('throws exception for unauthorized dynamic content', function () {
            Auth::login($this->user);
            
            $otherUser = User::factory()->create();
            $dynamicContent = DynamicContent::factory()->create([
                'user_id' => $otherUser->id,
                'type' => 'BUS_STOP'
            ]);

            $this->service->getBusStopContent($dynamicContent->id);
        })->throws(ModelNotFoundException::class);

        test('throws exception when bus stop content does not exist', function () {
            Auth::login($this->user);
            
            $dynamicContent = DynamicContent::factory()->create([
                'user_id' => $this->user->id,
                'type' => 'BUS_STOP'
            ]);

            $this->service->getBusStopContent($dynamicContent->id);
        })->throws(ModelNotFoundException::class);
    });

    describe('updateBusStopContent', function () {
        test('updates bus stop content successfully', function () {
            Auth::login($this->user);
            
            $dynamicContent = DynamicContent::factory()->create([
                'user_id' => $this->user->id,
                'type' => 'BUS_STOP'
            ]);

            $busStopContent = BusStop::create([
                'dynamic_content_id' => $dynamicContent->id,
                'stop_id' => 'BUS001',
                'name' => 'Old Stop Name',
                'address' => 'Old Address',
                'latitude' => -33.4382,
                'longitude' => -70.6508,
                'municipality_name' => 'Old Municipality'
            ]);

            $updateData = [
                'name' => 'New Stop Name',
                'municipality_name' => 'New Municipality',
                'municipality_website' => 'https://newmunicipality.cl'
            ];

            $result = $this->service->updateBusStopContent($busStopContent->id, $updateData);

            expect($result)->toBeInstanceOf(BusStop::class)
                ->and($result->id)->toBe($busStopContent->id)
                ->and($result->stop_id)->toBe('BUS001') // Should remain unchanged
                ->and($result->name)->toBe('New Stop Name')
                ->and($result->municipality_name)->toBe('New Municipality')
                ->and($result->municipality_website)->toBe('https://newmunicipality.cl');
        });

        test('throws exception for non-existent bus stop content', function () {
            Auth::login($this->user);

            $this->service->updateBusStopContent(99999, [
                'name' => 'New Name'
            ]);
        })->throws(ModelNotFoundException::class);

        test('throws exception for unauthorized bus stop content', function () {
            Auth::login($this->user);
            
            $otherUser = User::factory()->create();
            $dynamicContent = DynamicContent::factory()->create([
                'user_id' => $otherUser->id,
                'type' => 'BUS_STOP'
            ]);

            $busStopContent = BusStop::create([
                'dynamic_content_id' => $dynamicContent->id,
                'stop_id' => 'TEST001',
                'name' => 'Test Stop',
                'address' => 'Test Address',
                'latitude' => -33.4382,
                'longitude' => -70.6508,
                'municipality_name' => 'Test Municipality'
            ]);

            $this->service->updateBusStopContent($busStopContent->id, [
                'name' => 'New Name'
            ]);
        })->throws(ModelNotFoundException::class);

        test('updates only provided fields', function () {
            Auth::login($this->user);
            
            $dynamicContent = DynamicContent::factory()->create([
                'user_id' => $this->user->id,
                'type' => 'BUS_STOP'
            ]);

            $busStopContent = BusStop::create([
                'dynamic_content_id' => $dynamicContent->id,
                'stop_id' => 'BUS001',
                'name' => 'Original Name',
                'address' => 'Original Address',
                'latitude' => -33.4382,
                'longitude' => -70.6508,
                'municipality_name' => 'Original Municipality'
            ]);

            $result = $this->service->updateBusStopContent($busStopContent->id, [
                'name' => 'Updated Name'
            ]);

            expect($result->stop_id)->toBe('BUS001')
                ->and($result->name)->toBe('Updated Name')
                ->and($result->municipality_name)->toBe('Original Municipality');
        });
    });

    describe('deleteBusStopContent', function () {
        test('deletes bus stop content successfully', function () {
            Auth::login($this->user);
            
            $dynamicContent = DynamicContent::factory()->create([
                'user_id' => $this->user->id,
                'type' => 'BUS_STOP'
            ]);

            $busStopContent = BusStop::create([
                'dynamic_content_id' => $dynamicContent->id,
                'stop_id' => 'TEST001',
                'name' => 'Test Stop',
                'address' => 'Test Address',
                'latitude' => -33.4382,
                'longitude' => -70.6508,
                'municipality_name' => 'Test Municipality'
            ]);

            $result = $this->service->deleteBusStopContent($busStopContent->id);

            expect($result)->toBeTrue();
            
            // Verify deletion
            expect(BusStop::find($busStopContent->id))->toBeNull();
        });

        test('throws exception for non-existent bus stop content', function () {
            Auth::login($this->user);

            $this->service->deleteBusStopContent(99999);
        })->throws(ModelNotFoundException::class);

        test('throws exception for unauthorized bus stop content', function () {
            Auth::login($this->user);
            
            $otherUser = User::factory()->create();
            $dynamicContent = DynamicContent::factory()->create([
                'user_id' => $otherUser->id,
                'type' => 'BUS_STOP'
            ]);

            $busStopContent = BusStop::create([
                'dynamic_content_id' => $dynamicContent->id,
                'stop_id' => 'TEST001',
                'name' => 'Test Stop',
                'address' => 'Test Address',
                'latitude' => -33.4382,
                'longitude' => -70.6508,
                'municipality_name' => 'Test Municipality'
            ]);

            $this->service->deleteBusStopContent($busStopContent->id);
        })->throws(ModelNotFoundException::class);
    });

    describe('business logic and validation', function () {
        test('handles coordinate validation', function () {
            Auth::login($this->user);
            
            $dynamicContent = DynamicContent::factory()->create([
                'user_id' => $this->user->id,
                'type' => 'BUS_STOP'
            ]);

            // Valid coordinates for Chilean cities
            $validCoordinates = [
                ['latitude' => -33.4382, 'longitude' => -70.6508], // Santiago
                ['latitude' => -23.6509, 'longitude' => -70.3975], // Antofagasta
                ['latitude' => -36.8201, 'longitude' => -73.0444], // Concepción
                ['latitude' => -53.1638, 'longitude' => -70.9171], // Punta Arenas
            ];

            foreach ($validCoordinates as $coords) {
                $result = $this->service->createBusStopContent($dynamicContent->id, array_merge([
                    'stop_id' => 'TEST001',
                    'name' => 'Test Stop',
                    'address' => 'Test Address',
                    'municipality_name' => 'Test Municipality'
                ], $coords));
                
                expect($result->latitude)->toEqual($coords['latitude'])
                    ->and($result->longitude)->toEqual($coords['longitude']);
                
                // Clean up for next iteration
                $result->delete();
            }
        });

        test('handles different stop ID formats', function () {
            Auth::login($this->user);
            
            $dynamicContent = DynamicContent::factory()->create([
                'user_id' => $this->user->id,
                'type' => 'BUS_STOP'
            ]);

            $stopIdFormats = [
                'BUS001',
                'STOP-A1',
                'PA001',
                'SAN-001',
                'METRO-L1-001'
            ];

            foreach ($stopIdFormats as $stopId) {
                $result = $this->service->createBusStopContent($dynamicContent->id, [
                    'stop_id' => $stopId,
                    'name' => 'Test Stop',
                    'address' => 'Test Address',
                    'latitude' => -33.4382,
                    'longitude' => -70.6508,
                    'municipality_name' => 'Test Municipality'
                ]);
                
                expect($result->stop_id)->toBe($stopId);
                
                // Clean up for next iteration
                $result->delete();
            }
        });

        test('handles municipality data validation', function () {
            Auth::login($this->user);
            
            $dynamicContent = DynamicContent::factory()->create([
                'user_id' => $this->user->id,
                'type' => 'BUS_STOP'
            ]);

            $municipalityData = [
                [
                    'municipality_name' => 'Santiago',
                    'municipality_website' => 'https://santiago.cl',
                    'municipality_description' => 'Capital of Chile'
                ],
                [
                    'municipality_name' => 'Valparaíso',
                    'municipality_website' => 'https://municipalidaddevalparaiso.cl',
                    'municipality_description' => 'Port city and UNESCO World Heritage Site'
                ],
                [
                    'municipality_name' => 'La Serena',
                    'municipality_website' => 'https://laserena.cl',
                    'municipality_description' => 'Historic city in the north'
                ]
            ];

            foreach ($municipalityData as $data) {
                $result = $this->service->createBusStopContent($dynamicContent->id, array_merge([
                    'stop_id' => 'TEST001',
                    'name' => 'Test Stop',
                    'address' => 'Test Address',
                    'latitude' => -33.4382,
                    'longitude' => -70.6508,
                    'municipality_name' => 'Test Municipality'
                ], $data));
                
                expect($result->municipality_name)->toBe($data['municipality_name'])
                    ->and($result->municipality_website)->toBe($data['municipality_website'])
                    ->and($result->municipality_description)->toBe($data['municipality_description']);
                
                // Clean up for next iteration
                $result->delete();
            }
        });

        test('handles address format validation', function () {
            Auth::login($this->user);
            
            $dynamicContent = DynamicContent::factory()->create([
                'user_id' => $this->user->id,
                'type' => 'BUS_STOP'
            ]);

            $addressFormats = [
                'Av. Libertador Bernardo O\'Higgins 1234, Santiago',
                'Calle Arturo Prat 567, Valparaíso',
                'Plaza de Armas s/n, La Serena',
                'Av. Pedro de Valdivia 890, Providencia, Santiago',
                'Ruta 5 Norte Km 15, Colina'
            ];

            foreach ($addressFormats as $address) {
                $result = $this->service->createBusStopContent($dynamicContent->id, [
                    'stop_id' => 'TEST001',
                    'name' => 'Test Stop',
                    'address' => $address,
                    'latitude' => -33.4382,
                    'longitude' => -70.6508,
                    'municipality_name' => 'Test Municipality'
                ]);
                
                expect($result->address)->toBe($address);
                
                // Clean up for next iteration
                $result->delete();
            }
        });

        test('handles special characters in names', function () {
            Auth::login($this->user);
            
            $dynamicContent = DynamicContent::factory()->create([
                'user_id' => $this->user->id,
                'type' => 'BUS_STOP'
            ]);

            $specialNames = [
                'Estación Maipú',
                'Parada Ñuñoa',
                'Terminal O\'Higgins',
                'Metro Universidad de Chile',
                'Paradero 14 - Las Condes'
            ];

            foreach ($specialNames as $name) {
                $result = $this->service->createBusStopContent($dynamicContent->id, [
                    'stop_id' => 'TEST001',
                    'name' => $name,
                    'address' => 'Test Address',
                    'latitude' => -33.4382,
                    'longitude' => -70.6508,
                    'municipality_name' => 'Test Municipality'
                ]);
                
                expect($result->name)->toBe($name);
                
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
                'type' => 'BUS_STOP'
            ]);

            // Test with invalid coordinate that might cause DB error
            expect(fn() => $this->service->createBusStopContent($dynamicContent->id, [
                'latitude' => 'invalid-coordinate'
            ]))->toThrow(Exception::class);
        });

        test('handles unauthenticated user gracefully', function () {
            // No Auth::login() call
            
            $dynamicContent = DynamicContent::factory()->create([
                'user_id' => $this->user->id,
                'type' => 'BUS_STOP'
            ]);

            $this->service->createBusStopContent($dynamicContent->id, []);
        })->throws(ModelNotFoundException::class);
    });

    describe('relationship handling', function () {
        test('creates bus stop with relationships loaded', function () {
            Auth::login($this->user);
            
            $dynamicContent = DynamicContent::factory()->create([
                'user_id' => $this->user->id,
                'type' => 'BUS_STOP'
            ]);

            $result = $this->service->createBusStopContent($dynamicContent->id, [
                'stop_id' => 'BUS001',
                'name' => 'Test Stop with Relations',
                'address' => 'Test Address',
                'latitude' => -33.4382,
                'longitude' => -70.6508,
                'municipality_name' => 'Test Municipality'
            ]);

            // Verify the result has proper relationship structure
            expect($result)->toBeInstanceOf(BusStop::class)
                ->and($result->dynamic_content_id)->toBe($dynamicContent->id);
            
            // Verify that related models can be loaded (even if empty)
            expect($result->routes)->toBeEmpty()
                ->and($result->utilityPhones)->toBeEmpty();
        });

        test('maintains relationship integrity on update', function () {
            Auth::login($this->user);
            
            $dynamicContent = DynamicContent::factory()->create([
                'user_id' => $this->user->id,
                'type' => 'BUS_STOP'
            ]);

            $busStopContent = BusStop::create([
                'dynamic_content_id' => $dynamicContent->id,
                'stop_id' => 'BUS001',
                'name' => 'Test Stop',
                'address' => 'Test Address',
                'latitude' => -33.4382,
                'longitude' => -70.6508,
                'municipality_name' => 'Test Municipality'
            ]);

            $result = $this->service->updateBusStopContent($busStopContent->id, [
                'name' => 'Updated Stop Name'
            ]);

            // Verify relationship integrity is maintained
            expect($result->dynamic_content_id)->toBe($dynamicContent->id)
                ->and($result->id)->toBe($busStopContent->id);
        });
    });
});