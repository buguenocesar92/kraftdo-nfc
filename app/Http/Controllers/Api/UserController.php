<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * Get user status including onboarding information
     */
    public function status(): JsonResponse
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'status' => $user->status,
            ]
        ]);
    }

    /**
     * Update onboarding progress
     */
    public function updateOnboardingProgress(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        try {
            $validated = $request->validate([
                'step' => 'required|string',
                'completed' => 'required|boolean',
                'data' => 'sometimes|array'
            ]);

            $progress = [
                $validated['step'] => [
                    'completed' => $validated['completed'],
                    'completed_at' => now()->toISOString(),
                    'data' => $validated['data'] ?? null
                ]
            ];

            $user->updateOnboardingProgress($progress);

            return response()->json([
                'message' => 'Onboarding progress updated successfully',
                'progress' => $user->fresh()->onboarding_progress
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * Mark onboarding as completed
     */
    public function completeOnboarding(): JsonResponse
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $user->completeOnboarding();

        return response()->json([
            'message' => 'Onboarding completed successfully',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'status' => $user->fresh()->status,
            ]
        ]);
    }

    /**
     * Get user progress information
     */
    public function progress(): JsonResponse
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $nextStep = $this->getNextOnboardingStep($user);

        return response()->json([
            'current_progress' => $user->onboarding_progress ?? [],
            'next_step' => $nextStep,
            'completion_percentage' => $this->calculateCompletionPercentage($user),
            'recommended_actions' => $this->getRecommendedActions($user)
        ]);
    }

    /**
     * Get the next onboarding step for the user
     */
    private function getNextOnboardingStep($user): ?string
    {
        if ($user->onboarding_completed) {
            return null;
        }

        $progress = $user->onboarding_progress ?? [];

        // Define onboarding steps in order
        $steps = [
            'welcome' => 'Welcome page viewed',
            'content_type_selection' => 'Content type selected',
            'first_token_creation' => 'First token created',
            'dashboard_tour' => 'Dashboard tour completed'
        ];

        foreach ($steps as $step => $description) {
            if (!isset($progress[$step]) || !$progress[$step]['completed']) {
                return $step;
            }
        }

        return 'complete_onboarding';
    }

    /**
     * Calculate completion percentage
     */
    private function calculateCompletionPercentage($user): int
    {
        if ($user->onboarding_completed) {
            return 100;
        }

        $progress = $user->onboarding_progress ?? [];
        $totalSteps = 4; // welcome, content_type_selection, first_token_creation, dashboard_tour
        $completedSteps = 0;

        $steps = ['welcome', 'content_type_selection', 'first_token_creation', 'dashboard_tour'];
        
        foreach ($steps as $step) {
            if (isset($progress[$step]) && $progress[$step]['completed']) {
                $completedSteps++;
            }
        }

        return round(($completedSteps / $totalSteps) * 100);
    }

    /**
     * Get recommended actions for the user
     */
    private function getRecommendedActions($user): array
    {
        $actions = [];

        if ($user->onboarding_completed) {
            if ($user->nfcTokens()->count() === 0) {
                $actions[] = [
                    'type' => 'create_token',
                    'title' => 'Create your first NFC token',
                    'description' => 'Start sharing your content with NFC technology',
                    'action_url' => '/dashboard?action=create'
                ];
            } else {
                $actions[] = [
                    'type' => 'explore_features',
                    'title' => 'Explore advanced features',
                    'description' => 'Discover analytics, sharing options, and customization',
                    'action_url' => '/dashboard'
                ];
            }
        } else {
            $nextStep = $this->getNextOnboardingStep($user);
            
            switch ($nextStep) {
                case 'welcome':
                    $actions[] = [
                        'type' => 'start_onboarding',
                        'title' => 'Welcome to NFC Platform',
                        'description' => 'Take a quick tour to get started',
                        'action_url' => '/onboarding/welcome'
                    ];
                    break;
                    
                case 'content_type_selection':
                    $actions[] = [
                        'type' => 'select_content_type',
                        'title' => 'Choose your content type',
                        'description' => 'Select what type of content you want to share',
                        'action_url' => '/onboarding/token-type'
                    ];
                    break;
                    
                case 'first_token_creation':
                    $actions[] = [
                        'type' => 'create_first_token',
                        'title' => 'Create your first token',
                        'description' => 'Set up your content and create your first NFC token',
                        'action_url' => '/onboarding/create-token'
                    ];
                    break;
            }
        }

        return $actions;
    }
}