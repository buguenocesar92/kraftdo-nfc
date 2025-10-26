<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new user for SPA frontend
     */
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Assign NFC role to frontend users
        $user->assignRole('NFC');

        event(new Registered($user));

        // Create token for immediate login
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
            ],
            'message' => 'User registered successfully',
        ], 201)->cookie(
            'auth_token',
            $token,
            60 * 24 * 7, // 7 days
            '/', // path
            null, // domain
            request()->secure(), // secure (only HTTPS in production)
            true, // httpOnly
            false, // raw
            'lax' // sameSite
        );
    }


    /**
     * Login user and return token
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $this->ensureIsNotRateLimited($request);

        if (! Auth::attempt($request->only('email', 'password'))) {
            
            RateLimiter::hit($this->throttleKey($request), 300); // 5 minutes

            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }

        $user = Auth::user();

        // Verify user has NFC role (frontend users only)
        if (! $user->hasRole('NFC')) {
            Auth::logout();

            return response()->json([
                'message' => 'Access denied. Please use the admin panel.',
            ], 403);
        }

        RateLimiter::clear($this->throttleKey($request));

        // Delete old tokens if not remember me
        if (! $request->boolean('remember')) {
            $user->tokens()->delete();
        }

        $token = $user->createToken('auth-token')->plainTextToken;
        
        // Calculate cookie expiration based on remember me
        $cookieMinutes = $request->boolean('remember') ? 60 * 24 * 30 : 60 * 24; // 30 days or 1 day

        return response()->json([
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
            ],
            'message' => 'Login successful',
        ])->cookie(
            'auth_token',
            $token,
            $cookieMinutes,
            '/', // path
            null, // domain (works for same-origin)
            request()->secure(), // secure (let Laravel decide)
            true, // httpOnly
            false, // raw
            'lax' // sameSite
        );
    }

    /**
     * Logout user and revoke tokens
     */
    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user) {
            // Revoke current token
            $user->currentAccessToken()->delete();

            // Optionally revoke all tokens
            // $user->tokens()->delete();
        }

        return response()->json([
            'message' => 'Successfully logged out',
        ])->cookie(
            'auth_token',
            '', // empty value
            -1, // expire immediately
            '/', // path
            null, // domain
            request()->secure(), // secure (only HTTPS in production)
            true, // httpOnly
            false, // raw
            'lax' // sameSite
        );
    }

    /**
     * Get authenticated user data
     */
    public function user(Request $request): JsonResponse
    {
        
        $user = $request->user();

        $user->load('roles');

        return response()->json([
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->roles->map(function ($role) {
                    return [
                        'id' => $role->id,
                        'name' => $role->name,
                    ];
                }),
            ],
            'message' => 'User data retrieved successfully',
        ]);
    }

    /**
     * Legacy method for compatibility (alias for user)
     */
    public function me(Request $request): JsonResponse
    {
        return $this->user($request);
    }

    /**
     * Ensure the login request is not rate limited.
     */
    protected function ensureIsNotRateLimited(Request $request): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey($request), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey($request));

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ])->status(429);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    protected function throttleKey(Request $request): string
    {
        return Str::transliterate(Str::lower($request->input('email')) . '|' . $request->ip());
    }
}
