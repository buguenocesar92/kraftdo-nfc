<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\NfcToken;
use App\Models\DynamicContent;
use App\Models\ContentBusiness;
use App\Models\ContentProduct;
use Illuminate\Support\Str;

class BusinessDemoSeeder extends Seeder
{
    /**
     * Crear datos de ejemplo para un puesto de feria
     */
    public function run(): void
    {
        // Verificar si ya existe para evitar duplicados
        if (NfcToken::where('name', 'Puesto Artesanías La Feria')->exists()) {
            $this->command->info('BusinessDemo data already exists, skipping...');
            return;
        }

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

        // Crear token NFC para el negocio
        $token = NfcToken::updateOrCreate(
            ['name' => 'Puesto Artesanías La Feria'],
            [
            'user_id' => $user->id,
            'content_type' => 'BUSINESS',
            'customization_plan' => 'PREMIUM',
            'purchase_price' => 25000.00,
            'purchase_currency' => 'CLP',
            'purchased_at' => now(),
            'is_active' => true,
            ]
        );

        // Crear contenido dinámico
        $dynamicContent = DynamicContent::updateOrCreate(
            ['title' => 'Artesanías La Feria - Productos Locales'],
            [
            'user_id' => $user->id,
            'nfc_token_id' => $token->id,
            'content_id' => DynamicContent::generateUniqueContentId('BUSINESS'),
            'type' => 'BUSINESS',
            'description' => 'Puesto artesanal con productos únicos hechos a mano',
            'status' => 'published',
            'is_active' => true,
            'published_at' => now(),
            'data' => [
                'business_info' => [
                    'category' => 'Artesanías y Productos Locales',
                    'established_year' => 2008,
                    'owner_name' => 'Rosa Martinez',
                    'employees_count' => 3,
                    'specialties' => [
                        'Tejidos tradicionales mapuches',
                        'Cerámica artesanal',
                        'Productos naturales de la región',
                        'Miel de ulmo',
                        'Mermeladas caseras'
                    ]
                ],
                'location' => [
                    'address' => 'Feria Franklin, Local 45, Santiago, Región Metropolitana',
                    'coordinates' => [
                        'latitude' => -33.4569,
                        'longitude' => -70.6483
                    ],
                    'neighborhood' => 'Franklin',
                    'comuna' => 'Santiago',
                    'region' => 'Metropolitana'
                ],
                'contact_info' => [
                    'phone' => '+56 9 8765 4321',
                    'email' => 'artesanias@laferia.cl',
                    'website' => 'https://artesaniaslaferia.cl',
                    'whatsapp' => '+56987654321',
                    'instagram' => '@artesanias_laferia',
                    'facebook' => 'ArtesaniasLaFeria'
                ],
                'business_hours' => [
                    'monday' => 'Cerrado',
                    'tuesday' => '10:00-18:00',
                    'wednesday' => '10:00-18:00',
                    'thursday' => '10:00-18:00',
                    'friday' => '10:00-19:00',
                    'saturday' => '09:00-19:00',
                    'sunday' => '09:00-17:00'
                ],
                'services' => [
                    'Venta de productos artesanales',
                    'Tejidos a mano personalizados',
                    'Envío a domicilio en Santiago',
                    'Pago con tarjeta y transferencia',
                    'Embalaje especial para regalos',
                    'Asesoría en decoración rústica'
                ],
                'payment_methods' => [
                    'Efectivo',
                    'Tarjetas de débito/crédito',
                    'Transferencia bancaria',
                    'Mercado Pago'
                ],
                'certifications' => [
                    'Sello de Origen Indígena',
                    'Producto Artesanal Certificado',
                    'Comercio Justo'
                ]
            ]
            ]
        );

        // Crear negocio con datos completos
        $business = ContentBusiness::updateOrCreate(
            ['business_name' => 'Artesanías La Feria'],
            [
            'dynamic_content_id' => $dynamicContent->id,
            'description' => 'Puesto artesanal con productos únicos hechos a mano por artesanos locales. Especialistas en tejidos, cerámica y productos naturales de la región. Más de 15 años llevando tradición y calidad a nuestros clientes.',
            'business_type' => 'feria',
            'logo_url' => null, // Se puede agregar después
            'contact_phone' => '+56 9 8765 4321',
            'contact_email' => 'artesanias@laferia.cl',
            'contact_website' => 'https://artesaniaslaferia.cl',
            'address' => 'Feria Franklin, Local 45, Santiago, Región Metropolitana',
            'google_maps_url' => 'https://maps.google.com/maps?q=Feria+Franklin+Santiago',
            'google_reviews_url' => 'https://g.page/r/CBNeW_qBgKGlEAE/review',
            'google_place_id' => 'ChIJN165LjbZYpYRpVEG8L8m1k8',
            'instagram_url' => 'https://instagram.com/artesanias_laferia',
            'facebook_url' => 'https://facebook.com/ArtesaniasLaFeria',
            'whatsapp_number' => '+56987654321',
            'operating_hours' => [
                ['day' => 'monday', 'hours' => 'Cerrado'],
                ['day' => 'tuesday', 'hours' => '10:00-18:00'],
                ['day' => 'wednesday', 'hours' => '10:00-18:00'],
                ['day' => 'thursday', 'hours' => '10:00-18:00'],
                ['day' => 'friday', 'hours' => '10:00-19:00'],
                ['day' => 'saturday', 'hours' => '09:00-19:00'],
                ['day' => 'sunday', 'hours' => '09:00-17:00'],
            ],
            'services' => [
                'Productos artesanales',
                'Tejidos a mano',
                'Cerámica local',
                'Productos naturales',
                'Regalos únicos',
                'Envío a domicilio',
                'Pago con tarjeta'
            ],
            'catalog_enabled' => true,
            'color_palette' => [
                'primary' => '#8B4513',    // Marrón tierra
                'secondary' => '#D2691E',  // Naranja terracota  
                'accent' => '#228B22'      // Verde bosque
            ],
            ]
        );

        // Crear productos para el catálogo
        $products = [
            [
                'name' => 'Poncho de Alpaca',
                'price' => 45000.00,
                'currency' => 'CLP',
                'sku' => 'PONCHO-001',
                'stock' => 12,
                'in_stock' => true,
                'brand' => 'Artesanías La Feria',
                'specifications' => 'Poncho tradicional tejido en telar mapuche con 100% lana de alpaca. Diseños únicos inspirados en la cultura andina. Disponible en colores naturales: café, beige y gris.',
                'purchase_url' => null,
            ],
            [
                'name' => 'Jarrón de Greda',
                'price' => 18500.00,
                'currency' => 'CLP',
                'sku' => 'JARRON-002',
                'stock' => 8,
                'in_stock' => true,
                'brand' => 'Artesanías La Feria',
                'specifications' => 'Hermoso jarrón de greda hecho a mano por ceramistas locales. Decorado con motivos mapuches tradicionales. Perfecto para decoración o como regalo especial.',
                'purchase_url' => null,
            ],
            [
                'name' => 'Miel de Ulmo Pura',
                'price' => 12000.00,
                'currency' => 'CLP',
                'sku' => 'MIEL-003',
                'stock' => 25,
                'in_stock' => true,
                'brand' => 'Colmenas del Sur',
                'specifications' => 'Miel 100% natural de flores de Ulmo de la región de Los Ríos. Frasco de 500gr. Sin conservantes ni aditivos. Ideal para endulzar naturalmente o uso medicinal.',
                'purchase_url' => null,
            ],
            [
                'name' => 'Canasta de Mimbre',
                'price' => 8500.00,
                'currency' => 'CLP',
                'sku' => 'CANASTA-004',
                'stock' => 15,
                'in_stock' => true,
                'brand' => 'Artesanías La Feria',
                'specifications' => 'Canasta tejida en mimbre natural, perfecta para el hogar o como regalo. Tamaño mediano (30cm x 20cm). Ideal para pan, frutas o decoración.',
                'purchase_url' => null,
            ],
            [
                'name' => 'Mermelada de Mora Artesanal',
                'price' => 6500.00,
                'currency' => 'CLP',
                'sku' => 'MERMELADA-005',
                'stock' => 30,
                'in_stock' => true,
                'brand' => 'Dulces del Valle',
                'specifications' => 'Mermelada casera de mora silvestre sin conservantes. Frasco de 250gr. Hecha con frutas de temporada de pequeños productores locales.',
                'purchase_url' => null,
            ],
            [
                'name' => 'Bufanda de Lana Natural',
                'price' => 22000.00,
                'currency' => 'CLP',
                'sku' => 'BUFANDA-006',
                'stock' => 0,
                'in_stock' => false,
                'brand' => 'Artesanías La Feria',
                'specifications' => 'Bufanda tejida a mano en lana de oveja 100% natural. Suave y abrigadora. Colores disponibles: gris, café y crema. ¡Próximamente nueva temporada!',
                'purchase_url' => null,
            ]
        ];

        foreach ($products as $productData) {
            ContentProduct::updateOrCreate(
                ['sku' => $productData['sku']],
                array_merge($productData, [
                    'dynamic_content_id' => $dynamicContent->id,
                    'content_business_id' => $business->id,
                ])
            );
        }

        $this->command->info('✅ Creado puesto de feria "Artesanías La Feria" con:');
        $this->command->info("   🏪 Negocio completo con horarios y contacto");
        $this->command->info("   📱 Token NFC: {$token->token_id}");
        $this->command->info("   🛍️ {$business->products()->count()} productos en catálogo");
        $this->command->info("   🌐 URL: /token/{$token->token_id}");
        $this->command->info("   📋 Catálogo: /token/{$token->token_id}/products");
    }
}