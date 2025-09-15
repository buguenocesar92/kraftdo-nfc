<?php

use Tests\TestCase;
use App\Models\User;
use App\Models\NfcToken;
use App\Models\DynamicContent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(TestCase::class, RefreshDatabase::class);

describe('User Model', function () {
    
    test('puede crear un usuario', function () {
        $user = User::factory()->create();
        
        expect($user)->toBeInstanceOf(User::class)
            ->and($user->name)->toBeString()
            ->and($user->email)->toBeString()
            ->and($user->password)->toBeString();
    });

    test('hashea la contraseña automáticamente', function () {
        $user = User::factory()->create(['password' => 'test123']);
        
        expect(Hash::check('test123', $user->password))->toBeTrue();
    });

    test('email es único', function () {
        $user1 = User::factory()->create(['email' => 'test@example.com']);
        
        expect(fn() => User::factory()->create(['email' => 'test@example.com']))
            ->toThrow(\Illuminate\Database\QueryException::class);
    });

    test('tiene relación con tokens NFC', function () {
        $user = User::factory()->create();
        $tokens = NfcToken::factory()->count(3)->create(['user_id' => $user->id]);
        
        expect($user->nfcTokens)->toHaveCount(3);
        expect($user->nfcTokens->first())->toBeInstanceOf(NfcToken::class);
    });

    test('tiene relación con contenido dinámico', function () {
        $user = User::factory()->create();
        $contents = DynamicContent::factory()->count(5)->create(['user_id' => $user->id]);
        
        expect($user->dynamicContents)->toHaveCount(5);
        expect($user->dynamicContents->first())->toBeInstanceOf(DynamicContent::class);
    });

    test('oculta atributos sensibles en serialización', function () {
        $user = User::factory()->create();
        $array = $user->toArray();
        
        expect($array)->not->toHaveKey('password')
            ->and($array)->not->toHaveKey('remember_token');
    });

    test('verifica email correctamente', function () {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $unverifiedUser = User::factory()->create(['email_verified_at' => null]);
        
        expect($user->hasVerifiedEmail())->toBeTrue()
            ->and($unverifiedUser->hasVerifiedEmail())->toBeFalse();
    });

    test('implementa Authenticatable correctamente', function () {
        $user = User::factory()->create();
        
        expect(method_exists($user, 'getAuthIdentifier'))->toBeTrue();
    });

    test('implementa HasRoles trait', function () {
        $user = User::factory()->create();
        
        expect(method_exists($user, 'assignRole'))->toBeTrue()
            ->and(method_exists($user, 'hasRole'))->toBeTrue()
            ->and(method_exists($user, 'can'))->toBeTrue();
    });

    test('implementa Notifiable trait', function () {
        $user = User::factory()->create();
        
        expect(method_exists($user, 'notify'))->toBeTrue()
            ->and(method_exists($user, 'routeNotificationFor'))->toBeTrue();
    });

    test('implementa HasFactory trait', function () {
        $user = User::factory()->create();
        
        expect(method_exists($user, 'factory'))->toBeTrue();
    });

    test('maneja password hashing correctamente', function () {
        $user = User::factory()->create();
        
        expect($user->password)->toBeString()
            ->and(strlen($user->password))->toBeGreaterThan(10);
    });

    test('tiene fillable attributes correctos', function () {
        $user = new User();
        
        expect($user->getFillable())->toContain('name')
            ->and($user->getFillable())->toContain('email')
            ->and($user->getFillable())->toContain('password');
    });

    test('tiene casts correctos', function () {
        $user = User::factory()->create([
            'email_verified_at' => '2023-01-01 12:00:00'
        ]);
        
        expect($user->email_verified_at)->toBeInstanceOf(\Carbon\Carbon::class);
    });

    test('puede obtener tokens activos', function () {
        $user = User::factory()->create();
        NfcToken::factory()->count(3)->create([
            'user_id' => $user->id,
            'is_active' => true
        ]);
        NfcToken::factory()->count(2)->create([
            'user_id' => $user->id,
            'is_active' => false
        ]);
        
        $activeTokens = $user->nfcTokens()->where('is_active', true)->get();
        
        expect($activeTokens)->toHaveCount(3);
    });

    test('puede obtener contenido publicado', function () {
        $user = User::factory()->create();
        DynamicContent::factory()->count(4)->create([
            'user_id' => $user->id,
            'status' => 'published'
        ]);
        DynamicContent::factory()->count(2)->create([
            'user_id' => $user->id,
            'status' => 'draft'
        ]);
        
        $publishedContent = $user->dynamicContents()->where('status', 'published')->get();
        
        expect($publishedContent)->toHaveCount(4);
    });

    test('tiene configuración correcta de factory', function () {
        $user = User::factory()->make();
        
        expect($user->name)->toBeString()
            ->and($user->email)->toContain('@')
            ->and($user->password)->toBeString();
    });

    test('maneja timestamps correctamente', function () {
        $user = User::factory()->create();
        
        expect($user->created_at)->toBeInstanceOf(\Carbon\Carbon::class)
            ->and($user->updated_at)->toBeInstanceOf(\Carbon\Carbon::class);
    });

    test('puede ser guardado y recuperado de la base de datos', function () {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password123')
        ];
        
        $user = User::create($userData);
        $retrievedUser = User::find($user->id);
        
        expect($retrievedUser->name)->toBe('Test User')
            ->and($retrievedUser->email)->toBe('test@example.com')
            ->and(Hash::check('password123', $retrievedUser->password))->toBeTrue();
    });
});