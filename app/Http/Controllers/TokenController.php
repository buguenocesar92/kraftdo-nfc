<?php

namespace App\Http\Controllers;

use App\Enums\ContentType;
use App\Models\NfcToken;
use App\Services\AnalyticsService;
use App\Services\TokenService;
use App\Services\ViewingLockService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TokenController extends Controller
{
    public function __construct(
        private TokenService $tokenService,
        private AnalyticsService $analyticsService,
        private ViewingLockService $viewingLock,
    ) {
    }

    /**
     * Show token content as JSON API
     */
    public function show(Request $request, string $tokenId): JsonResponse
    {
        $isPhysical = $this->viewingLock->isPhysicalScan($request);

        // Link compartido bloqueado mientras alguien lo está viendo físicamente
        if (! $isPhysical && $this->viewingLock->hasLock($tokenId)) {
            return response()->json([
                'message' => 'Este cuadro está siendo visualizado en este momento. Inténtalo en unos segundos.',
                'status' => 423,
                'retry_after' => ViewingLockService::VIEWING_TTL,
            ], 423);
        }

        // First try to get token with cached content (for tokens with content)
        $tokenData = $this->tokenService->getTokenWithContent($tokenId);

        if ($tokenData && $this->tokenService->validateToken($tokenData)) {
            $token = $tokenData['token'];

            // Validate content type is supported
            if (! ContentType::isSupported($token->content_type)) {
                return $this->tokenService->handleNotFound($request, 'Tipo de contenido no disponible');
            }

            // Handle inactive tokens
            if (! $token->is_active) {
                return $this->tokenService->handleInactiveToken($request, $token);
            }

            // Record analytics asynchronously
            $this->analyticsService->recordAccess($tokenData);

            // Scan físico: activar/renovar bandera de visualización
            if ($isPhysical) {
                $this->viewingLock->setLock($tokenId);
            }

            // Render response based on content type
            return $this->tokenService->renderResponse($request, $tokenData);
        }

        // If cached content not found, try to get basic token info (for content management)
        $token = NfcToken::where('token_id', $tokenId)->first();

        if (! $token) {
            return $this->tokenService->handleNotFound($request, 'Token no encontrado');
        }

        // Handle inactive tokens even without dynamic content
        if (! $token->is_active) {
            return $this->tokenService->handleInactiveToken($request, $token);
        }

        // Scan físico sin contenido aún: activar bandera igual
        if ($isPhysical) {
            $this->viewingLock->setLock($tokenId);
        }

        // Return token with null dynamic content (for content management interface)
        return response()->json([
            'data' => [
                'token' => $token,
                'dynamicContent' => $token->dynamicContent,
                'content' => [],
            ],
            'message' => 'Token obtenido exitosamente',
            'status' => 200,
        ], 200);
    }

    /**
     * Renueva el TTL de la bandera de visualización activa (heartbeat del frontend).
     */
    public function heartbeat(string $tokenId): JsonResponse
    {
        $renewed = $this->viewingLock->refreshLock($tokenId);

        return response()->json([
            'renewed' => $renewed,
            'message' => $renewed
                ? 'Visualización renovada.'
                : 'No hay visualización activa para este token.',
            'retry_after' => $renewed ? ViewingLockService::VIEWING_TTL : null,
        ]);
    }

    /**
     * Show business products catalog as JSON API
     */
    public function showProducts(Request $request, string $tokenId): JsonResponse
    {
        // Get token data
        $tokenData = $this->tokenService->getTokenWithContent($tokenId);

        if (! $this->tokenService->validateToken($tokenData)) {
            return $this->tokenService->handleNotFound($request);
        }

        $token = $tokenData['token'];
        $content = $tokenData['content'];

        // Validate it's a business token
        if ($token->content_type !== ContentType::BUSINESS->value) {
            return $this->tokenService->handleNotFound($request, 'Esta página solo está disponible para negocios');
        }

        // Handle inactive tokens
        if (! $token->is_active) {
            return $this->tokenService->handleInactiveToken($request, $token);
        }

        $contentBusiness = $content['business'];

        // Verify catalog is enabled
        if (! $contentBusiness || ! $contentBusiness->catalog_enabled) {
            return $this->tokenService->handleNotFound($request, 'Catálogo no disponible para este negocio');
        }

        // Get products
        $products = $contentBusiness->products()->get();

        // Record analytics
        $this->analyticsService->recordAccess($tokenData);

        return response()->json([
            'data' => [
                'token' => $token,
                'dynamicContent' => $tokenData['dynamicContent'],
                'contentBusiness' => $contentBusiness,
                'products' => $products,
            ],
            'message' => 'Productos obtenidos exitosamente',
            'status' => 200,
        ]);
    }

    // ========================================
    // CRUD METHODS FOR AUTHENTICATED USERS
    // ========================================

    /**
     * Display a listing of the user's tokens
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        $query = NfcToken::where('user_id', $user->id)
            ->with(['dynamicContent'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->has('content_type') && $request->content_type !== '') {
            $query->where('content_type', $request->content_type);
        }

        if ($request->has('is_active') && $request->is_active !== '') {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('token_id', 'like', "%{$search}%");
            });
        }

        // Pagination
        $perPage = min($request->get('per_page', 12), 50); // Max 50 per page
        $tokens = $query->paginate($perPage);

        return response()->json([
            'data' => $tokens->items(),
            'meta' => [
                'current_page' => $tokens->currentPage(),
                'last_page' => $tokens->lastPage(),
                'per_page' => $tokens->perPage(),
                'total' => $tokens->total(),
                'from' => $tokens->firstItem(),
                'to' => $tokens->lastItem(),
            ],
            'message' => 'Tokens obtenidos exitosamente',
        ]);
    }

    /**
     * Store a newly created token
     */
    public function store(Request $request): JsonResponse
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'content_type' => 'required|string|in:PROFILE,BUSINESS,GIFT,EVENT,TOURIST,BUS_STOP',
            'customization_plan' => 'nullable|string|in:BASIC,STANDARD,PREMIUM,DELUXE',
        ]);

        $token = NfcToken::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'content_type' => $request->content_type,
            'customization_plan' => $request->customization_plan ?? 'BASIC',
            'is_active' => true,
        ]);

        // Load relationships for response
        $token->load(['dynamicContent']);

        return response()->json([
            'data' => $token,
            'message' => 'Token creado exitosamente',
        ], 201);
    }

    /**
     * Update the specified token
     */
    public function update(Request $request, string $tokenId): JsonResponse
    {
        $user = Auth::user();

        $token = NfcToken::where('user_id', $user->id)->find($tokenId);
        if (!$token) {
            return response()->json([
                'message' => 'Token not found or access denied'
            ], 404);
        }

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'content_type' => 'sometimes|required|string|in:PROFILE,BUSINESS,GIFT,EVENT,TOURIST,BUS_STOP',
            'customization_plan' => 'nullable|string|in:BASIC,STANDARD,PREMIUM,DELUXE',
            'is_active' => 'sometimes|boolean',
        ]);

        $token->update($request->only([
            'name',
            'content_type', 
            'customization_plan',
            'is_active'
        ]));

        $token->load(['dynamicContent']);

        return response()->json([
            'data' => $token,
            'message' => 'Token actualizado exitosamente',
        ]);
    }

    /**
     * Remove the specified token
     */
    public function destroy(string $tokenId): JsonResponse
    {
        $user = Auth::user();

        $token = NfcToken::where('user_id', $user->id)->find($tokenId);
        if (!$token) {
            return response()->json([
                'message' => 'Token not found or access denied'
            ], 404);
        }

        // Delete associated dynamic content if exists
        if ($token->dynamicContent) {
            $token->dynamicContent->delete();
        }

        $token->delete();

        return response()->json([
            'message' => 'Token eliminado exitosamente',
        ]);
    }
}
