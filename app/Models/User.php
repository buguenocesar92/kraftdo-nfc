<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthentication;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthenticationRecovery;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, HasAppAuthentication, HasAppAuthenticationRecovery
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use Notifiable;
    use HasRoles;
    use HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'onboarding_completed',
        'onboarding_completed_at',
        'onboarding_progress',
        'is_first_time_user',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'app_authentication_secret',
        'app_authentication_recovery_codes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'onboarding_completed' => 'boolean',
            'onboarding_completed_at' => 'datetime',
            'onboarding_progress' => 'array',
            'is_first_time_user' => 'boolean',
            'app_authentication_secret' => 'encrypted',
            'app_authentication_recovery_codes' => 'encrypted:array',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    /**
     * Get all dynamic contents belonging to this user
     */
    public function dynamicContents()
    {
        return $this->hasMany(DynamicContent::class);
    }

    /**
     * Get all NFC tokens belonging to this user
     */
    public function nfcTokens()
    {
        return $this->hasMany(NfcToken::class);
    }

    /**
     * Determine if the user can access the given Filament panel.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->can('access_admin_panel');
    }

    /**
     * Check if user needs onboarding
     */
    public function needsOnboarding(): bool
    {
        return $this->is_first_time_user || !$this->onboarding_completed;
    }

    /**
     * Mark onboarding as completed
     */
    public function completeOnboarding(): void
    {
        $this->update([
            'onboarding_completed' => true,
            'onboarding_completed_at' => now(),
            'is_first_time_user' => false,
        ]);
    }

    /**
     * Update onboarding progress
     */
    public function updateOnboardingProgress(array $progress): void
    {
        $this->update([
            'onboarding_progress' => array_merge($this->onboarding_progress ?? [], $progress)
        ]);
    }

    /**
     * Get user status for API
     */
    public function getStatusAttribute(): array
    {
        return [
            'is_first_time_user' => $this->is_first_time_user,
            'onboarding_completed' => $this->onboarding_completed,
            'has_tokens' => $this->nfcTokens()->count() > 0,
            'tokens_count' => $this->nfcTokens()->count(),
            'needs_onboarding' => $this->needsOnboarding(),
            'onboarding_progress' => $this->onboarding_progress,
        ];
    }

    /**
     * Get the app authentication secret.
     */
    public function getAppAuthenticationSecret(): ?string
    {
        return $this->app_authentication_secret;
    }

    /**
     * Save the app authentication secret.
     */
    public function saveAppAuthenticationSecret(?string $secret): void
    {
        $this->app_authentication_secret = $secret;
        $this->save();
    }

    /**
     * Get the app authentication holder name.
     */
    public function getAppAuthenticationHolderName(): string
    {
        return $this->name;
    }

    /**
     * Get the app authentication recovery codes.
     */
    public function getAppAuthenticationRecoveryCodes(): ?array
    {
        return $this->app_authentication_recovery_codes;
    }

    /**
     * Save the app authentication recovery codes.
     */
    public function saveAppAuthenticationRecoveryCodes(?array $codes): void
    {
        $this->app_authentication_recovery_codes = $codes;
        $this->save();
    }
}
