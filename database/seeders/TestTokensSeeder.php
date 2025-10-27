<?php

namespace Database\Seeders;

use App\Models\NfcToken;
use App\Models\User;
use Illuminate\Database\Seeder;

class TestTokensSeeder extends Seeder
{
    public function run(): void
    {
        // Find test user
        $user = User::where('email', 'patypuv@mailinator.com')->first();
        
        if (!$user) {
            $this->command->error('User patypuv@mailinator.com not found');
            return;
        }

        // Create test tokens
        $tokens = [
            [
                'name' => 'Mi Perfil Personal',
                'content_type' => 'PROFILE',
                'customization_plan' => 'STANDARD',
                'is_active' => true,
                'total_investment_views' => 145,
            ],
            [
                'name' => 'Negocio - Café Central',
                'content_type' => 'BUSINESS', 
                'customization_plan' => 'PREMIUM',
                'is_active' => true,
                'total_investment_views' => 387,
            ],
            [
                'name' => 'Regalo Cumpleaños',
                'content_type' => 'GIFT',
                'customization_plan' => 'BASIC',
                'is_active' => false,
                'total_investment_views' => 23,
            ],
            [
                'name' => 'Evento - Conferencia Tech',
                'content_type' => 'EVENT',
                'customization_plan' => 'PREMIUM',
                'is_active' => true,
                'total_investment_views' => 89,
            ],
        ];

        foreach ($tokens as $tokenData) {
            $token = NfcToken::create([
                'user_id' => $user->id,
                ...$tokenData
            ]);
            
            $this->command->info("Token created: {$token->name} (ID: {$token->token_id})");
        }

        $this->command->info('Test tokens created successfully!');
    }
}