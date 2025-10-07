<?php

namespace Database\Seeders;

use App\Models\BusStop;
use App\Models\Route;
use App\Models\Schedule;
use App\Models\UtilityPhone;
use App\Models\DynamicContent;
use App\Models\NfcToken;
use App\Models\User;
use Illuminate\Database\Seeder;

class BusStopDemoSeeder extends Seeder
{
    public function run(): void
    {
        // Usar el Super Administrator existente
        $user = User::find(1); // Super Administrator
        if (!$user) {
            $user = User::where('email', 'admin@kraftdo-nfc.com')->first();
        }
        
        if (!$user) {
            throw new \Exception('Super Administrator no encontrado. Asegúrate de que existe un usuario con ID 1 o email admin@kraftdo-nfc.com');
        }

        // Crear token NFC para el paradero
        $nfcToken = NfcToken::create([
            'user_id' => $user->id,
            'name' => 'Paradero Plaza de Armas - Machalí',
            'content_type' => 'BUS_STOP', // Nuevo tipo de contenido
            'customization_plan' => 'BASIC',
            'purchase_price' => 8000, // 8.000 CLP
            'purchase_currency' => 'CLP',
            'purchased_at' => now()->subDays(15),
            'purchase_notes' => 'Token NFC para paradero de demostración',
            'is_active' => true,
        ]);

        // Crear contenido dinámico para el paradero
        $dynamicContent = DynamicContent::create([
            'content_id' => DynamicContent::generateUniqueContentId('BUS_STOP'),
            'type' => 'BUS_STOP',
            'title' => 'Paradero Plaza de Armas',
            'description' => 'Paradero principal ubicado en el corazón de Machalí, conecta con todas las líneas de transporte público de la comuna.',
            'image_url' => null,
            'data' => [],
            'is_active' => true,
            'status' => 'published',
            'published_at' => now(),
            'user_id' => $user->id,
            'nfc_token_id' => $nfcToken->id,
        ]);

        // Crear el paradero
        $busStop = BusStop::create([
            'dynamic_content_id' => $dynamicContent->id,
            'stop_id' => 'PAR001',
            'name' => 'Plaza de Armas',
            'address' => 'Plaza de Armas s/n, Machalí, Región de O\'Higgins',
            'latitude' => -34.1833,
            'longitude' => -70.6500,
            'municipality_name' => 'Machalí',
            'municipality_logo_url' => '/images/logos/machali.png',
            'municipality_description' => 'Machalí, comuna cordillerana de la Región de O\'Higgins, conocida por su belleza natural, tradiciones mineras y cercanía a centros de esquí. Puerta de entrada a la cordillera de Los Andes.',
            'municipality_website' => 'https://www.machali.cl',
            'is_active' => true,
        ]);

        // Sincronizar referencia en DynamicContent
        $dynamicContent->update(['bus_stop_id' => $busStop->id]);

        // Crear rutas de transporte
        $routes = [
            [
                'name' => 'Línea 1 - Centro/Hospital',
                'route_number' => '1',
                'origin' => 'Terminal Machalí',
                'destination' => 'Hospital Regional Rancagua',
                'fare' => 800,
                'currency' => 'CLP',
                'operator' => 'Buses Machalí',
                'color' => '#DC2626',
                'is_active' => true,
            ],
            [
                'name' => 'Línea 2 - Sewell/Mina',
                'route_number' => '2',
                'origin' => 'Plaza de Armas Machalí',
                'destination' => 'Sewell',
                'fare' => 1200,
                'currency' => 'CLP',
                'operator' => 'Transporte Cordillera',
                'color' => '#059669',
                'is_active' => true,
            ],
            [
                'name' => 'Línea 3 - Universidad',
                'route_number' => '3',
                'origin' => 'Machalí Centro',
                'destination' => 'Universidad de O\'Higgins',
                'fare' => 900,
                'currency' => 'CLP',
                'operator' => 'Buses Universitarios',
                'color' => '#2563EB',
                'is_active' => true,
            ]
        ];

        foreach ($routes as $routeData) {
            $route = Route::create(array_merge($routeData, [
                'bus_stop_id' => $busStop->id,
            ]));

            // Crear horarios para cada ruta
            $schedules = [
                // Horarios de lunes a viernes
                [
                    'route_id' => $route->id,
                    'day_of_week' => 'monday',
                    'departure_times' => [
                        '06:00', '06:30', '07:00', '07:30', '08:00', '08:30',
                        '09:00', '10:00', '11:00', '12:00', '13:00', '14:00',
                        '15:00', '16:00', '17:00', '17:30', '18:00', '18:30',
                        '19:00', '19:30', '20:00', '21:00', '22:00'
                    ],
                    'frequency_minutes' => 30,
                    'notes' => 'Horario regular días hábiles',
                    'is_active' => true,
                ],
                [
                    'route_id' => $route->id,
                    'day_of_week' => 'saturday',
                    'departure_times' => [
                        '07:00', '08:00', '09:00', '10:00', '11:00', '12:00',
                        '13:00', '14:00', '15:00', '16:00', '17:00', '18:00',
                        '19:00', '20:00', '21:00'
                    ],
                    'frequency_minutes' => 60,
                    'notes' => 'Horario reducido sábados',
                    'is_active' => true,
                ],
                [
                    'route_id' => $route->id,
                    'day_of_week' => 'sunday',
                    'departure_times' => [
                        '08:00', '10:00', '12:00', '14:00', '16:00', '18:00', '20:00'
                    ],
                    'frequency_minutes' => 120,
                    'notes' => 'Horario dominical limitado',
                    'is_active' => true,
                ]
            ];

            foreach ($schedules as $scheduleData) {
                Schedule::create($scheduleData);
            }
        }

        // Crear teléfonos de utilidad pública
        $utilityPhones = [
            [
                'name' => 'Bomberos Machalí',
                'phone_number' => '132',
                'category' => 'emergencia',
                'description' => 'Cuerpo de Bomberos de Machalí - Emergencias',
                'icon' => '🚒',
                'is_emergency' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Carabineros Machalí',
                'phone_number' => '133',
                'category' => 'emergencia',
                'description' => 'Carabineros de Chile - Comisaría Machalí',
                'icon' => '👮',
                'is_emergency' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Hospital Machalí',
                'phone_number' => '+56 72 229 8000',
                'category' => 'salud',
                'description' => 'Hospital de Machalí - Urgencias médicas',
                'icon' => '🏥',
                'is_emergency' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Municipalidad Machalí',
                'phone_number' => '+56 72 229 5000',
                'category' => 'municipal',
                'description' => 'Ilustre Municipalidad de Machalí',
                'icon' => '🏛️',
                'is_emergency' => false,
                'is_active' => true,
            ],
            [
                'name' => 'CGE Emergencias',
                'phone_number' => '800 800 767',
                'category' => 'servicios',
                'description' => 'Compañía General de Electricidad - Cortes de luz',
                'icon' => '⚡',
                'is_emergency' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Essal Emergencias',
                'phone_number' => '600 623 7725',
                'category' => 'servicios',
                'description' => 'Empresa de Servicios Sanitarios - Cortes de agua',
                'icon' => '💧',
                'is_emergency' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Radio Taxi Machalí',
                'phone_number' => '+56 72 229 1234',
                'category' => 'transporte',
                'description' => 'Servicio de radio taxi 24 horas',
                'icon' => '🚖',
                'is_emergency' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Taxi Cordillera',
                'phone_number' => '+56 9 8765 4321',
                'category' => 'transporte',
                'description' => 'Taxi ejecutivo para traslados a centros de esquí',
                'icon' => '🚖',
                'is_emergency' => false,
                'is_active' => true,
            ]
        ];

        foreach ($utilityPhones as $phoneData) {
            UtilityPhone::create(array_merge($phoneData, [
                'bus_stop_id' => $busStop->id,
            ]));
        }

        $this->command->info('✅ Seeder del Paradero Plaza de Armas ejecutado correctamente');
        $this->command->info("🏷️  Token NFC ID: {$nfcToken->token_id}");
        $this->command->info("🌐 URL por token: /nfc/{$nfcToken->token_id}");
        $this->command->info("🌐 URL por content: /c/{$dynamicContent->content_id}");
        $this->command->info("🚏 Paradero ID: {$busStop->stop_id}");
        $this->command->info("🚌 Se crearon " . count($routes) . " rutas de transporte");
        $this->command->info("📞 Se crearon " . count($utilityPhones) . " teléfonos de utilidad");
        $this->command->info("🕐 Se crearon horarios para días de semana, sábados y domingos");
    }
}