<?php

namespace Database\Seeders;

use App\Models\DynamicContent;
use App\Models\ContentBusinessGroup;
use App\Models\ContentBusiness;
use App\Models\NfcToken;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BusinessGroupDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Usar el Super Administrator existente
        $user = User::where('email', 'admin@kraftdo-nfc.com')->first();
        if (!$user) {
            $user = User::whereHas('roles', function($query) {
                $query->where('name', 'Super Admin');
            })->first();
        }
        
        if (!$user) {
            throw new \Exception('Super Administrator no encontrado. Ejecuta AdminUserSeeder primero.');
        }

        // IDs fijos para evitar duplicados
        $groupTokenId = 'ecoparque-machali-group-uuid-001';
        $groupContentId = 'ecoparque-machali-content-uuid-001';

        // 1. Crear el token NFC para el grupo (Ecoparque Machalí)
        $groupToken = NfcToken::updateOrCreate(
            ['token_id' => $groupTokenId],
            [
                'user_id' => $user->id,
                'name' => 'Ecoparque Machalí - Food Court',
                'content_type' => 'BUSINESS_GROUP',
                'customization_plan' => 'PREMIUM',
                'purchase_price' => 75.00,
                'purchased_at' => now()->subDays(20),
                'purchase_currency' => 'USD',
                'is_active' => true,
            ]
        );

        // 2. Crear el contenido dinámico del grupo
        $groupDynamicContent = DynamicContent::updateOrCreate(
            ['content_id' => $groupContentId],
            [
                'user_id' => $user->id,
                'nfc_token_id' => $groupToken->id,
                'type' => DynamicContent::TYPE_BUSINESS_GROUP,
                'tier' => 'premium',
                'title' => '🌳 Ecoparque Machalí',
                'description' => 'El destino gastronómico y de esparcimiento familiar más importante de la Región de O\'Higgins. Disfruta de la naturaleza mientras saboreas lo mejor de la gastronomía local.',
                'data' => [
                    'highlights' => [
                        'Más de 8 food trucks especializados',
                        'Espacios verdes para toda la familia',
                        'Estacionamiento gratuito',
                        'WiFi gratuito en todo el parque',
                        'Área de juegos infantiles',
                        'Senderos ecológicos'
                    ],
                    'events' => [
                        'Ferias gastronómicas los fines de semana',
                        'Música en vivo todos los viernes',
                        'Talleres de cocina para niños'
                    ]
                ],
                'image_url' => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=800',
                'status' => 'published',
                'published_at' => now()->subDays(18),
                'is_active' => true,
            ]
        );

        // 3. Crear el Business Group
        $businessGroup = $groupDynamicContent->createOrUpdateBusinessGroup([
            'group_name' => 'Ecoparque Machalí',
            'description' => 'Un oasis gastronómico en el corazón de Machalí, donde la naturaleza se encuentra con los mejores sabores de la región. Nuestro food court ecológico ofrece una experiencia única con opciones para todos los gustos.',
            'address' => 'Ruta 5 Sur Km 83, Machalí, Región de O\'Higgins, Chile',
            'location_coordinates' => [
                'lat' => -34.1853,
                'lng' => -70.6506
            ],
            'contact_phone' => '+56 9 8765 4321',
            'contact_email' => 'info@ecoparquemachali.cl',
            'contact_website' => 'https://ecoparquemachali.cl',
            'operating_hours' => [
                'monday' => '10:00-22:00',
                'tuesday' => '10:00-22:00',
                'wednesday' => '10:00-22:00',
                'thursday' => '10:00-22:00',
                'friday' => '10:00-23:00',
                'saturday' => '09:00-23:00',
                'sunday' => '09:00-22:00'
            ],
            'group_type' => 'food_court',
            'amenities' => [
                'parking',
                'wifi',
                'restrooms',
                'playground',
                'eco_trails',
                'live_music',
                'family_area',
                'pet_friendly'
            ],
            'special_instructions' => 'Los viernes por la noche contamos con música en vivo. Los fines de semana organizamos ferias gastronómicas especiales. Mascotas bienvenidas en áreas designadas.',
            'logo_url' => 'https://images.unsplash.com/photo-1518709268805-4e9042af2176?w=200',
            'banner_image' => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=1200'
        ]);

        // 4. Crear food trucks individuales que formarán parte del grupo
        $foodTrucks = [
            [
                'name' => 'El Asado Perfecto',
                'description' => 'Especialistas en carnes a la parrilla y empanadas artesanales',
                'business_type' => 'restaurant',
                'logo' => 'https://images.unsplash.com/photo-1544025162-d76694265947?w=200',
                'specialties' => ['Asado', 'Empanadas', 'Choripan'],
                'is_featured' => true
            ],
            [
                'name' => 'Mariscos del Sur',
                'description' => 'Frescos mariscos y pescados de la costa chilena',
                'business_type' => 'restaurant',
                'logo' => 'https://images.unsplash.com/photo-1565680018434-b513d5e5fd47?w=200',
                'specialties' => ['Ceviche', 'Paila Marina', 'Salmón Grillado']
            ],
            [
                'name' => 'Pizza Artesanal',
                'description' => 'Pizzas al horno de leña con ingredientes locales',
                'business_type' => 'restaurant',
                'logo' => 'https://images.unsplash.com/photo-1513104890138-7c749659a591?w=200',
                'specialties' => ['Pizza Margherita', 'Pizza Chilena', 'Calzone']
            ],
            [
                'name' => 'Sabores de Oriente',
                'description' => 'Auténtica comida asiática fusion',
                'business_type' => 'restaurant',
                'logo' => 'https://images.unsplash.com/photo-1617093727343-374698b1b08d?w=200',
                'specialties' => ['Sushi', 'Ramen', 'Pad Thai'],
                'is_featured' => true
            ],
            [
                'name' => 'Dulces Tentaciones',
                'description' => 'Postres artesanales y café de especialidad',
                'business_type' => 'restaurant',
                'logo' => 'https://images.unsplash.com/photo-1488477181946-6428a0291777?w=200',
                'specialties' => ['Tortas', 'Café de Especialidad', 'Helados Artesanales']
            ],
            [
                'name' => 'Veggie Paradise',
                'description' => 'Opciones veganas y vegetarianas saludables',
                'business_type' => 'restaurant',
                'logo' => 'https://images.unsplash.com/photo-1511690743698-d9d85f2fbf38?w=200',
                'specialties' => ['Buddha Bowl', 'Hamburguesas Veganas', 'Smoothies']
            ]
        ];

        // Crear cada food truck como negocio individual
        foreach ($foodTrucks as $index => $truckData) {
            // Crear token para el food truck
            $truckTokenId = "food-truck-{$index}-uuid-" . str_pad($index + 1, 3, '0', STR_PAD_LEFT);
            $truckContentId = "food-truck-content-{$index}-uuid-" . str_pad($index + 1, 3, '0', STR_PAD_LEFT);

            $truckToken = NfcToken::updateOrCreate(
                ['token_id' => $truckTokenId],
                [
                    'user_id' => $user->id,
                    'name' => $truckData['name'] . ' - Food Truck',
                    'content_type' => 'BUSINESS',
                    'customization_plan' => 'STANDARD',
                    'purchase_price' => 35.00,
                    'purchased_at' => now()->subDays(15 - $index),
                    'purchase_currency' => 'USD',
                    'is_active' => true,
                ]
            );

            // Crear contenido dinámico del food truck
            $truckDynamicContent = DynamicContent::updateOrCreate(
                ['content_id' => $truckContentId],
                [
                    'user_id' => $user->id,
                    'nfc_token_id' => $truckToken->id,
                    'type' => DynamicContent::TYPE_BUSINESS,
                    'tier' => 'standard',
                    'title' => $truckData['name'],
                    'description' => $truckData['description'],
                    'data' => [
                        'specialties' => $truckData['specialties'],
                        'location_note' => 'Ubicado en Ecoparque Machalí',
                        'parent_venue' => 'Ecoparque Machalí'
                    ],
                    'image_url' => $truckData['logo'],
                    'status' => 'published',
                    'published_at' => now()->subDays(13 - $index),
                    'is_active' => true,
                ]
            );

            // Crear el negocio
            $business = $truckDynamicContent->createOrUpdateBusiness([
                'business_name' => $truckData['name'],
                'description' => $truckData['description'],
                'business_type' => $truckData['business_type'],
                'logo_url' => $truckData['logo'],
                'contact_phone' => "+56 9 " . (87654321 + $index),
                'contact_email' => strtolower(str_replace(' ', '', $truckData['name'])) . '@ecoparquemachali.cl',
                'address' => 'Ecoparque Machalí, Local ' . chr(65 + $index), // A, B, C, etc.
                'operating_hours' => [
                    'general' => 'Lun-Dom: 11:00-21:00'
                ],
                'catalog_enabled' => true
            ]);

            // Añadir el food truck al grupo
            $businessGroup->addMember($business, [
                'display_order' => $index,
                'is_featured' => $truckData['is_featured'] ?? false,
                'member_status' => 'active',
                'member_notes' => 'Food truck especializado en ' . implode(', ', $truckData['specialties'])
            ]);
        }

        $this->command->info('🏪 Business Group demo data created successfully!');
        $this->command->info('🌳 Ecoparque Machalí: /token/' . $groupTokenId);
        $this->command->info('📊 Created ' . count($foodTrucks) . ' food trucks as members');
        $this->command->info('🔗 Group members can be accessed individually through their own tokens');
    }
}