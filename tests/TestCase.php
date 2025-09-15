<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use App\Models\NfcToken;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Limpiar cache en cada test
        Cache::flush();
    }

    /**
     * Crear usuario de prueba con permisos admin
     */
    protected function createAdminUser(): User
    {
        return User::factory()->create([
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);
    }

    /**
     * Crear usuario regular de prueba
     */
    protected function createUser(): User
    {
        return User::factory()->create([
            'email' => 'user@test.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);
    }

    /**
     * Crear token NFC de prueba
     */
    protected function createNfcToken(array $attributes = []): NfcToken
    {
        return NfcToken::factory()->create($attributes);
    }

    /**
     * Autenticar como admin
     */
    protected function actingAsAdmin(): self
    {
        return $this->actingAs($this->createAdminUser());
    }

    /**
     * Autenticar como usuario regular
     */
    protected function actingAsUser(): self
    {
        return $this->actingAs($this->createUser());
    }
}
