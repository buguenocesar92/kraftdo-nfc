<?php

use App\Http\Middleware\CheckAdminPermission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

describe('CheckAdminPermission Middleware', function () {
    beforeEach(function () {
        $this->middleware = new CheckAdminPermission();
        $this->request = Request::create('/admin', 'GET');

        // Crear permiso necesario
        Permission::create(['name' => 'access_admin_panel']);
    });

    test('redirige a login si usuario no está autenticado', function () {
        $response = $this->middleware->handle($this->request, function () {
            return response('Success');
        });

        expect($response->getStatusCode())->toBe(302);
        expect($response->headers->get('Location'))->toContain('login');
    });

    test('permite acceso a usuario con permisos', function () {
        $user = User::factory()->create();
        $user->givePermissionTo('access_admin_panel');

        Auth::login($user);

        $response = $this->middleware->handle($this->request, function () {
            return response('Success');
        });

        expect($response->getContent())->toBe('Success');
    });

    test('retorna 403 para usuario sin permisos', function () {
        $user = User::factory()->create();
        // No asignar el permiso access_admin_panel

        Auth::login($user);

        expect(fn () => $this->middleware->handle($this->request, function () {
            return response('Success');
        }))->toThrow(\Symfony\Component\HttpKernel\Exception\HttpException::class);
    });

    test('permite acceso a usuario autenticado con permisos correctos', function () {
        $user = User::factory()->create();
        $user->givePermissionTo('access_admin_panel');

        $this->actingAs($user);

        $response = $this->middleware->handle($this->request, function () {
            return response('Admin Panel Access Granted');
        });

        expect($response->getContent())->toBe('Admin Panel Access Granted');
        expect($response->getStatusCode())->toBe(200);
    });

    test('verifica mensaje de error correcto para usuario sin permisos', function () {
        $user = User::factory()->create();
        Auth::login($user);

        try {
            $this->middleware->handle($this->request, function () {
                return response('Success');
            });

            // No debería llegar aquí
            expect(false)->toBeTrue();
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            expect($e->getStatusCode())->toBe(403);
            expect($e->getMessage())->toContain('No tienes permisos para acceder al panel de administración');
        }
    });
});
