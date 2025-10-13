<?php

namespace Database\Seeders;

use App\Models\DynamicContent;
use App\Models\NfcToken;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class NfcDemoSeeder extends Seeder
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

        // Primero, actualizar cualquier token MENU existente a BUSINESS
        \App\Models\NfcToken::where('content_type', 'MENU')->update(['content_type' => 'BUSINESS']);
        
        // También actualizar cualquier contenido dinámico de tipo MENU a BUSINESS
        \App\Models\DynamicContent::where('type', 'MENU')->update(['type' => 'BUSINESS']);
        
        // Usar UUIDs fijos para evitar duplicados
        $giftTokenId = 'gift-demo-token-uuid-fixed-001';
        $businessTokenId = 'menu-demo-token-uuid-fixed-002'; // Reusar el ID del menu existente
        $profileTokenId = 'profile-demo-token-uuid-fixed-003';

        // Crear tokens NFC de demo
        $tokens = [
            [
                'token_id' => $giftTokenId,
                'name' => 'Token Gift Demo',
                'content_type' => 'GIFT',
                'customization_plan' => 'PREMIUM',
                'purchase_price' => 25.00,
                'purchased_at' => now()->subDays(10),
                'purchase_currency' => 'USD',
                'is_active' => true,
            ],
            [
                'token_id' => $businessTokenId,
                'name' => 'Token Business Demo',
                'content_type' => 'BUSINESS',
                'customization_plan' => 'STANDARD',
                'purchase_price' => 35.00,
                'purchased_at' => now()->subDays(15),
                'purchase_currency' => 'USD',
                'is_active' => true,
            ],
            [
                'token_id' => $profileTokenId,
                'name' => 'Token Profile Demo',
                'content_type' => 'PROFILE',
                'customization_plan' => 'BASIC',
                'purchase_price' => 15.00,
                'purchased_at' => now()->subDays(5),
                'purchase_currency' => 'USD',
                'is_active' => true,
            ]
        ];

        $createdTokens = [];
        foreach ($tokens as $tokenData) {
            $token = NfcToken::updateOrCreate(
                ['token_id' => $tokenData['token_id']],
                array_merge($tokenData, ['user_id' => $user->id])
            );
            $createdTokens[] = $token;
        }

        // Usar content_ids fijos para evitar duplicados
        $giftContentId = 'gift-demo-content-uuid-fixed-001';
        $businessContentId = 'menu-demo-content-uuid-fixed-002'; // Reusar el ID del contenido menu existente
        $profileContentId = 'profile-demo-content-uuid-fixed-003';
        
        $contents = [
            [
                'content_id' => $giftContentId,
                'type' => DynamicContent::TYPE_GIFT,
                'gift_subtype' => 'anniversary',
                'tier' => 'sweet',
                'title' => '💕 Feliz Aniversario Mi Amor',
                'description' => 'Cada día contigo es una nueva aventura. Gracias por ser mi compañera de vida y por llenar mis días de alegría y amor.',
                'data' => [
                    'gift_message' => 'Han sido los mejores años de mi vida. Te amo más cada día que pasa. ¡Feliz aniversario!',
                    'sender' => 'Carlos',
                    'recipient' => 'María',
                    'background_music' => null,
                    'social_links' => [
                        [
                            'platform' => 'instagram',
                            'url' => 'https://instagram.com/carlos_y_maria'
                        ]
                    ]
                ],
                'image_url' => 'https://images.unsplash.com/photo-1518811179048-5844bd0ae0ae?w=400',
                'status' => 'published',
                'published_at' => now()->subDays(8),
                'is_active' => true,
                'nfc_token_id' => $createdTokens[0]->id,
            ],
            [
                'content_id' => $businessContentId,
                'type' => DynamicContent::TYPE_BUSINESS,
                'tier' => 'luxury',
                'title' => 'Restaurante La Bella Italia',
                'description' => 'Auténtica cocina italiana en el corazón de la ciudad. Ingredientes frescos y recetas tradicionales.',
                'data' => [
                    'restaurant_info' => [
                        'phone' => '+1 555-0123',
                        'address' => 'Calle Principal 123, Centro',
                        'hours' => 'Lun-Dom: 12:00 - 23:00'
                    ],
                    'menu_items' => [
                        [
                            'name' => 'Pizza Margherita',
                            'description' => 'Base de tomate, mozzarella fresca, albahaca',
                            'price' => 18.50,
                            'category' => 'Pizzas',
                            'image' => 'https://images.unsplash.com/photo-1574071318508-1cdbab80d002?w=300'
                        ],
                        [
                            'name' => 'Pasta Carbonara',
                            'description' => 'Espaguetis con huevo, queso pecorino y panceta',
                            'price' => 22.00,
                            'category' => 'Pastas',
                            'image' => 'https://images.unsplash.com/photo-1551892374-ecf8746a99c7?w=300'
                        ],
                        [
                            'name' => 'Risotto ai Funghi',
                            'description' => 'Arroz cremoso con setas porcini',
                            'price' => 24.00,
                            'category' => 'Risottos',
                            'image' => 'https://images.unsplash.com/photo-1476124369491-e7addf5db371?w=300'
                        ]
                    ]
                ],
                'image_url' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=400',
                'status' => 'published',
                'published_at' => now()->subDays(12),
                'is_active' => true,
                'nfc_token_id' => $createdTokens[1]->id,
            ],
            [
                'content_id' => $profileContentId,
                'type' => DynamicContent::TYPE_PROFILE,
                'tier' => 'sweet',
                'title' => 'Ana García - Diseñadora Gráfica',
                'description' => 'Diseñadora gráfica especializada en branding e identidad visual. Apasionada por crear experiencias visuales únicas.',
                'data' => [
                    'contact_info' => [
                        'email' => 'ana.garcia@email.com',
                        'phone' => '+1 555-0456',
                        'website' => 'https://anagarcia.design'
                    ],
                    'social_links' => [
                        [
                            'platform' => 'instagram',
                            'url' => 'https://instagram.com/ana_designs'
                        ],
                        [
                            'platform' => 'linkedin',
                            'url' => 'https://linkedin.com/in/anagarcia'
                        ]
                    ],
                    'skills' => ['Diseño Gráfico', 'Branding', 'Ilustración', 'UI/UX'],
                    'bio' => 'Con más de 5 años de experiencia en diseño gráfico, me especializo en crear identidades visuales que conecten con las audiencias.'
                ],
                'image_url' => 'https://images.unsplash.com/photo-1494790108755-2616b612b547?w=400',
                'status' => 'published',
                'published_at' => now()->subDays(3),
                'is_active' => true,
                'nfc_token_id' => $createdTokens[2]->id,
            ]
        ];

        foreach ($contents as $contentData) {
            $dynamicContent = DynamicContent::updateOrCreate(
                ['content_id' => $contentData['content_id']],
                array_merge($contentData, ['user_id' => $user->id])
            );

            // Crear contenido especializado según el tipo
            $this->createSpecializedContent($dynamicContent, $contentData);
        }

        $this->command->info('✅ Demo NFC data created successfully!');
        $this->command->info('🔑 Demo user: demo@nfc.com / password');
        $this->command->info('📱 Test URLs:');
        $this->command->info("   - Gift: /token/{$giftTokenId}");
        $this->command->info("   - Business: /token/{$businessTokenId}"); 
        $this->command->info("   - Profile: /token/{$profileTokenId}");
    }

    private function createSpecializedContent($dynamicContent, $contentData)
    {
        switch ($dynamicContent->type) {
            case DynamicContent::TYPE_GIFT:
                $this->createGiftContent($dynamicContent, $contentData);
                break;
            case DynamicContent::TYPE_BUSINESS:
                $this->createRestaurantContent($dynamicContent, $contentData);
                break;
            case DynamicContent::TYPE_PROFILE:
                $this->createProfileContent($dynamicContent, $contentData);
                break;
        }
    }

    private function createGiftContent($dynamicContent, $contentData)
    {
        $giftData = $contentData['data'];
        
        $gift = \App\Models\ContentGift::updateOrCreate(
            ['dynamic_content_id' => $dynamicContent->id],
            [
                'message' => $giftData['gift_message'] ?? 'Un regalo especial para ti',
                'sender_name' => $giftData['sender'] ?? 'Carlos',
                'recipient_name' => $giftData['recipient'] ?? 'María',
            ]
        );

        // Actualizar referencia en DynamicContent
        $dynamicContent->update(['gift_id' => $gift->id]);
    }

    private function createRestaurantContent($dynamicContent, $contentData)
    {
        $menuData = $contentData['data'];
        
        // Crear el business como restaurante
        $business = \App\Models\ContentBusiness::updateOrCreate(
            ['dynamic_content_id' => $dynamicContent->id],
            [
                'business_name' => $dynamicContent->title,
                'description' => $dynamicContent->description,
                'business_type' => 'restaurant',
                'contact_phone' => $menuData['restaurant_info']['phone'] ?? null,
                'address' => $menuData['restaurant_info']['address'] ?? null,
                'operating_hours' => $menuData['restaurant_info']['hours'] ? 
                    ['general' => $menuData['restaurant_info']['hours']] : null,
                'catalog_enabled' => true,
            ]
        );

        // Crear elementos del menú como productos
        if (isset($menuData['menu_items'])) {
            foreach ($menuData['menu_items'] as $item) {
                \App\Models\ContentProduct::updateOrCreate(
                    [
                        'dynamic_content_id' => $dynamicContent->id,
                        'content_business_id' => $business->id,
                        'name' => $item['name']
                    ],
                    [
                        'price' => $item['price'],
                        'currency' => $item['currency'] ?? 'USD',
                        'brand' => $item['category'], // Categoría como brand
                        'specifications' => $item['description'],
                        'stock' => 999, // Stock alto para items de menú
                        'in_stock' => true,
                    ]
                );
            }
        }
    }

    private function createProfileContent($dynamicContent, $contentData)
    {
        $profileData = $contentData['data'];
        
        $profile = \App\Models\ContentProfile::updateOrCreate(
            ['dynamic_content_id' => $dynamicContent->id],
            [
                'name' => explode(' - ', $dynamicContent->title)[0] ?? 'Ana García',
                'profession' => 'Diseñadora Gráfica',
                'bio' => $profileData['bio'] ?? $dynamicContent->description,
                'contact_email' => $profileData['contact_info']['email'] ?? null,
                'contact_phone' => $profileData['contact_info']['phone'] ?? null,
                'contact_website' => $profileData['contact_info']['website'] ?? null,
                'location' => 'Santiago, Chile',
                'color_palette' => [
                    'primary' => '#3B82F6',
                    'secondary' => '#8B5CF6',
                    'accent' => '#F59E0B'
                ],
            ]
        );

        // Crear habilidades
        if (isset($profileData['skills'])) {
            foreach ($profileData['skills'] as $skillName) {
                \App\Models\ContentSkill::updateOrCreate(
                    [
                        'dynamic_content_id' => $dynamicContent->id,
                        'name' => $skillName
                    ],
                    [
                        'level' => 8, // Nivel de 1-10, 8 = avanzado
                        'category' => 'technical',
                    ]
                );
            }
        }

        // Crear enlaces sociales
        if (isset($profileData['social_links'])) {
            foreach ($profileData['social_links'] as $link) {
                \App\Models\ContentSocialLink::updateOrCreate(
                    [
                        'dynamic_content_id' => $dynamicContent->id,
                        'platform' => $link['platform']
                    ],
                    [
                        'url' => $link['url'],
                        'username' => basename($link['url']),
                    ]
                );
            }
        }

        // Actualizar referencia en DynamicContent
        $dynamicContent->update(['profile_id' => $profile->id]);
    }
}