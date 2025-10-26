<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);
});

test('can register a new user', function () {
    $userData = [
        'name' => fake()->name(),
        'email' => fake()->unique()->safeEmail(),
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ];

    $response = $this->postJson('/api/register', $userData);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'data' => [
                'user' => ['id', 'name', 'email'],
            ],
            'message',
        ])
        ->assertCookie('auth_token'); // Verify cookie is set

    $this->assertDatabaseHas('users', [
        'name' => $userData['name'],
        'email' => $userData['email'],
    ]);

    $user = User::where('email', $userData['email'])->first();
    expect($user->hasRole('NFC'))->toBeTrue();
});

test('validates registration data', function () {
    // Test missing fields
    $response = $this->postJson('/api/register', []);
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'email', 'password']);

    // Test invalid email
    $response = $this->postJson('/api/register', [
        'name' => 'Test User',
        'email' => 'invalid-email',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);

    // Test password confirmation mismatch
    $response = $this->postJson('/api/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'different123',
    ]);
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['password']);
});

test('prevents duplicate email registration', function () {
    $existingUser = User::factory()->create();

    $response = $this->postJson('/api/register', [
        'name' => 'New User',
        'email' => $existingUser->email,
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

test('can login with valid credentials', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('password123'),
    ]);
    $user->assignRole('NFC');

    $response = $this->postJson('/api/login', [
        'email' => 'test@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'user' => ['id', 'name', 'email'],
            ],
            'message',
        ])
        ->assertCookie('auth_token'); // Verify cookie is set
});

test('rejects invalid login credentials', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('password123'),
    ]);

    // Wrong password
    $response = $this->postJson('/api/login', [
        'email' => 'test@example.com',
        'password' => 'wrongpassword',
    ]);
    $response->assertStatus(401)
        ->assertJson(['message' => 'Invalid credentials']);

    // Non-existent user
    $response = $this->postJson('/api/login', [
        'email' => 'nonexistent@example.com',
        'password' => 'password123',
    ]);
    $response->assertStatus(401)
        ->assertJson(['message' => 'Invalid credentials']);
});

test('validates login data', function () {
    $response = $this->postJson('/api/login', []);
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email', 'password']);

    $response = $this->postJson('/api/login', [
        'email' => 'invalid-email',
        'password' => '',
    ]);
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email', 'password']);
});

test('can logout authenticated user', function () {
    $user = User::factory()->create();
    $user->assignRole('NFC');
    Sanctum::actingAs($user);

    $response = $this->postJson('/api/logout');

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Successfully logged out',
        ])
        ->assertCookieExpired('auth_token'); // Verify cookie is expired
});

test('can get authenticated user data', function () {
    $user = User::factory()->create();
    $user->assignRole('NFC');
    Sanctum::actingAs($user);

    $response = $this->getJson('/api/user');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => ['id', 'name', 'email', 'roles'],
            'message',
        ])
        ->assertJson([
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
});

test('requires authentication for user endpoint', function () {
    $response = $this->getJson('/api/user');
    $response->assertStatus(401);
});

test('requires authentication for logout', function () {
    $response = $this->postJson('/api/logout');
    $response->assertStatus(401);
});

test('includes user roles in response', function () {
    $user = User::factory()->create();
    $user->assignRole('NFC');
    Sanctum::actingAs($user);

    $response = $this->getJson('/api/user');

    $response->assertStatus(200)
        ->assertJsonPath('data.roles.0.name', 'NFC');
});

test('can handle remember me functionality', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('password123'),
    ]);
    $user->assignRole('NFC');

    $response = $this->postJson('/api/login', [
        'email' => 'test@example.com',
        'password' => 'password123',
        'remember' => true,
    ]);

    $response->assertStatus(200)
        ->assertCookie('auth_token'); // Verify cookie is set

    // Verify token is created
    $this->assertDatabaseHas('personal_access_tokens', [
        'tokenable_id' => $user->id,
        'tokenable_type' => User::class,
    ]);
});

test('rate limits login attempts', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('password123'),
    ]);

    // Make multiple failed attempts
    for ($i = 0; $i < 6; $i++) {
        $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);
    }

    // Next attempt should be rate limited
    $response = $this->postJson('/api/login', [
        'email' => 'test@example.com',
        'password' => 'wrongpassword',
    ]);

    $response->assertStatus(429); // Too Many Requests
});

test('prevents admin users from logging in via SPA', function () {
    $adminUser = User::factory()->create([
        'email' => 'admin@example.com',
        'password' => Hash::make('password123'),
    ]);
    $adminUser->assignRole('Admin'); // Admin role, not NFC

    $response = $this->postJson('/api/login', [
        'email' => 'admin@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(403)
        ->assertJson(['message' => 'Access denied. Please use the admin panel.']);
});
