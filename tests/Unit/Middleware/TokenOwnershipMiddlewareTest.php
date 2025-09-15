<?php

use Tests\TestCase;
use App\Http\Middleware\TokenOwnershipMiddleware;
use App\Models\User;
use App\Models\NfcToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;

uses(TestCase::class, RefreshDatabase::class);

describe('TokenOwnershipMiddleware', function () {
    
    beforeEach(function () {
        $this->middleware = new TokenOwnershipMiddleware();
        
        // Crear permiso necesario
        Permission::create(['name' => 'configure_own_tokens']);
    });

    test('permite continuar si no hay tokenId en la ruta', function () {
        $request = Request::create('/some-route', 'GET');
        
        $response = $this->middleware->handle($request, function () {
            return response('Success');
        });
        
        expect($response->getContent())->toBe('Success');
    });

    test('retorna 403 si usuario no está autenticado', function () {
        // Simular una ruta con tokenId
        Route::get('/test/{tokenId}', function () {
            return 'test';
        })->name('test.route');
        
        $request = Request::create('/test/123', 'GET');
        $request->setRouteResolver(function () {
            $route = new \Illuminate\Routing\Route(['GET'], '/test/{tokenId}', function () {});
            $route->bind($request = Request::create('/test/123', 'GET'));
            $route->setParameter('tokenId', '123');
            return $route;
        });
        
        expect(fn() => $this->middleware->handle($request, function () {
            return response('Success');
        }))->toThrow(\Symfony\Component\HttpKernel\Exception\HttpException::class);
    });

    test('retorna 404 si token no existe', function () {
        $user = User::factory()->create();
        $user->givePermissionTo('configure_own_tokens');
        Auth::login($user);
        
        $request = Request::create('/test/99999', 'GET');
        $request->setRouteResolver(function () {
            $route = new \Illuminate\Routing\Route(['GET'], '/test/{tokenId}', function () {});
            $route->bind($request = Request::create('/test/99999', 'GET'));
            $route->setParameter('tokenId', '99999');
            return $route;
        });
        
        expect(fn() => $this->middleware->handle($request, function () {
            return response('Success');
        }))->toThrow(\Symfony\Component\HttpKernel\Exception\HttpException::class);
    });

    test('retorna 403 si token pertenece a otro usuario', function () {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $other->givePermissionTo('configure_own_tokens');
        
        $token = NfcToken::factory()->create(['user_id' => $owner->id]);
        
        Auth::login($other);
        
        $request = Request::create("/test/{$token->id}", 'GET');
        $request->setRouteResolver(function () use ($token) {
            $route = new \Illuminate\Routing\Route(['GET'], '/test/{tokenId}', function () {});
            $route->bind($request = Request::create("/test/{$token->id}", 'GET'));
            $route->setParameter('tokenId', (string)$token->id);
            return $route;
        });
        
        expect(fn() => $this->middleware->handle($request, function () {
            return response('Success');
        }))->toThrow(\Symfony\Component\HttpKernel\Exception\HttpException::class);
    });

    test('retorna 403 si usuario no tiene permisos para configurar tokens', function () {
        $user = User::factory()->create();
        // No asignar el permiso configure_own_tokens
        
        $token = NfcToken::factory()->create(['user_id' => $user->id]);
        
        Auth::login($user);
        
        $request = Request::create("/test/{$token->id}", 'GET');
        $request->setRouteResolver(function () use ($token) {
            $route = new \Illuminate\Routing\Route(['GET'], '/test/{tokenId}', function () {});
            $route->bind($request = Request::create("/test/{$token->id}", 'GET'));
            $route->setParameter('tokenId', (string)$token->id);
            return $route;
        });
        
        expect(fn() => $this->middleware->handle($request, function () {
            return response('Success');
        }))->toThrow(\Symfony\Component\HttpKernel\Exception\HttpException::class);
    });

    test('permite acceso a propietario con permisos correctos', function () {
        $user = User::factory()->create();
        $user->givePermissionTo('configure_own_tokens');
        
        $token = NfcToken::factory()->create(['user_id' => $user->id]);
        
        Auth::login($user);
        
        $request = Request::create("/test/{$token->id}", 'GET');
        $request->setRouteResolver(function () use ($token) {
            $route = new \Illuminate\Routing\Route(['GET'], '/test/{tokenId}', function () {});
            $route->bind($request = Request::create("/test/{$token->id}", 'GET'));
            $route->setParameter('tokenId', (string)$token->id);
            return $route;
        });
        
        $response = $this->middleware->handle($request, function () {
            return response('Access Granted');
        });
        
        expect($response->getContent())->toBe('Access Granted');
        expect($response->getStatusCode())->toBe(200);
    });

    test('verifica mensajes de error correctos', function () {
        $user = User::factory()->create();
        Auth::login($user);
        
        $request = Request::create('/test/99999', 'GET');
        $request->setRouteResolver(function () {
            $route = new \Illuminate\Routing\Route(['GET'], '/test/{tokenId}', function () {});
            $route->bind($request = Request::create('/test/99999', 'GET'));
            $route->setParameter('tokenId', '99999');
            return $route;
        });
        
        try {
            $this->middleware->handle($request, function () {
                return response('Success');
            });
            
            expect(false)->toBeTrue(); // No debería llegar aquí
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            expect($e->getStatusCode())->toBe(404);
            expect($e->getMessage())->toContain('Token no encontrado');
        }
    });

    test('verifica mensaje de error para acceso no autorizado', function () {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $other->givePermissionTo('configure_own_tokens');
        
        $token = NfcToken::factory()->create(['user_id' => $owner->id]);
        
        Auth::login($other);
        
        $request = Request::create("/test/{$token->id}", 'GET');
        $request->setRouteResolver(function () use ($token) {
            $route = new \Illuminate\Routing\Route(['GET'], '/test/{tokenId}', function () {});
            $route->bind($request = Request::create("/test/{$token->id}", 'GET'));
            $route->setParameter('tokenId', (string)$token->id);
            return $route;
        });
        
        try {
            $this->middleware->handle($request, function () {
                return response('Success');
            });
            
            expect(false)->toBeTrue(); // No debería llegar aquí
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            expect($e->getStatusCode())->toBe(403);
            expect($e->getMessage())->toContain('No tienes permisos para acceder a este token');
        }
    });
});