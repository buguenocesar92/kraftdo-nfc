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
        // Crear usuario demo si no existe
        $user = User::firstOrCreate(
            ['email' => 'demo@nfc.com'],
            [
                'name' => 'Usuario Demo',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        // Crear tokens NFC de demo
        $tokens = [
            [
                'token_id' => Str::uuid()->toString(),
                'name' => 'Token Gift Demo',
                'content_type' => 'GIFT',
                'customization_plan' => 'PREMIUM',
                'purchase_price' => 25.00,
                'purchased_at' => now()->subDays(10),
                'purchase_currency' => 'USD',
                'is_active' => true,
            ],
            [
                'token_id' => Str::uuid()->toString(),
                'name' => 'Token Menu Demo',
                'content_type' => 'MENU',
                'customization_plan' => 'STANDARD',
                'purchase_price' => 35.00,
                'purchased_at' => now()->subDays(15),
                'purchase_currency' => 'USD',
                'is_active' => true,
            ],
            [
                'token_id' => Str::uuid()->toString(),
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

        // Crear contenido dinámico para cada token
        $giftContentId = Str::uuid()->toString();
        $menuContentId = Str::uuid()->toString();
        $profileContentId = Str::uuid()->toString();
        
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
                'content_id' => $menuContentId,
                'type' => DynamicContent::TYPE_MENU,
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
            DynamicContent::updateOrCreate(
                ['content_id' => $contentData['content_id']],
                array_merge($contentData, ['user_id' => $user->id])
            );
        }

        $this->command->info('✅ Demo NFC data created successfully!');
        $this->command->info('🔑 Demo user: demo@nfc.com / password');
        $this->command->info('📱 Test URLs:');
        $this->command->info("   - Gift: /c/{$giftContentId}");
        $this->command->info("   - Menu: /c/{$menuContentId}"); 
        $this->command->info("   - Profile: /c/{$profileContentId}");
        $this->command->info('   - By Token: /t/{token_uuid}');
    }
}