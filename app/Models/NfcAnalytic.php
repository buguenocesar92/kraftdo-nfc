<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class NfcAnalytic extends Model
{
    use HasFactory;

    protected $fillable = [
        'content_id',
        'content_type',
        'nfc_token_id',
        'ip_address',
        'user_agent',
        'country',
        'city',
        'device_type',
        'browser',
        'referrer',
        'is_unique_visit',
        'accessed_at',
    ];

    protected $casts = [
        'is_unique_visit' => 'boolean',
        'accessed_at' => 'datetime',
    ];

    /**
     * Relación con el token NFC
     */
    public function nfcToken(): BelongsTo
    {
        return $this->belongsTo(NfcToken::class);
    }

    /**
     * Registrar un acceso al contenido
     */
    public static function recordAccess(string $contentId, string $contentType, ?int $nfcTokenId = null): void
    {
        $request = request();
        $ipAddress = $request->ip();
        $userAgent = $request->userAgent();

        // Verificar si es una visita única (primera vez que esta IP accede a este contenido)
        $isUniqueVisit = ! self::where('content_id', $contentId)
            ->where('ip_address', $ipAddress)
            ->exists();

        // Detectar tipo de dispositivo básico
        $deviceType = self::detectDeviceType($userAgent);
        $browser = self::detectBrowser($userAgent);

        self::create([
            'content_id' => $contentId,
            'content_type' => $contentType,
            'nfc_token_id' => $nfcTokenId,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'device_type' => $deviceType,
            'browser' => $browser,
            'referrer' => $request->header('referer'),
            'is_unique_visit' => $isUniqueVisit,
            'accessed_at' => now(),
        ]);

        // Actualizar last_used_at en el token si existe y incrementar contador ROI
        if ($nfcTokenId) {
            $token = NfcToken::find($nfcTokenId);
            if ($token) {
                $token->update(['last_used_at' => now()]);
                $token->incrementViews(); // Incrementar contador para ROI
            }
        }
    }

    /**
     * Obtener estadísticas para un contenido específico con métricas financieras
     */
    public static function getStatsForContent(string $contentId): array
    {
        // Obtener el token asociado para métricas ROI
        $token = NfcToken::whereHas('dynamicContent', function ($query) use ($contentId) {
            $query->where('content_id', $contentId);
        })->first();

        $baseStats = [
            'total_views' => self::where('content_id', $contentId)->count(),
            'unique_views' => self::where('content_id', $contentId)->where('is_unique_visit', true)->count(),
            'last_access' => self::where('content_id', $contentId)->latest('accessed_at')->first()?->accessed_at,
            'views_today' => self::where('content_id', $contentId)->whereDate('accessed_at', today())->count(),
            'views_this_week' => self::where('content_id', $contentId)->whereBetween('accessed_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'views_this_month' => self::where('content_id', $contentId)->whereMonth('accessed_at', now()->month)->count(),
            'top_countries' => self::where('content_id', $contentId)
                ->select('country', DB::raw('count(*) as total'))
                ->whereNotNull('country')
                ->groupBy('country')
                ->orderByDesc('total')
                ->limit(5)
                ->get(),
            'device_breakdown' => self::where('content_id', $contentId)
                ->select('device_type', DB::raw('count(*) as total'))
                ->whereNotNull('device_type')
                ->groupBy('device_type')
                ->orderByDesc('total')
                ->get(),
            'browser_breakdown' => self::where('content_id', $contentId)
                ->select('browser', DB::raw('count(*) as total'))
                ->whereNotNull('browser')
                ->groupBy('browser')
                ->orderByDesc('total')
                ->limit(5)
                ->get(),
            'daily_views' => self::where('content_id', $contentId)
                ->select(
                    DB::raw('DATE(accessed_at) as date'),
                    DB::raw('count(*) as views')
                )
                ->whereBetween('accessed_at', [now()->subDays(30), now()])
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
        ];

        // Agregar métricas financieras si existe el token
        if ($token) {
            $baseStats['financial_metrics'] = $token->getROI();
        } else {
            $baseStats['financial_metrics'] = null;
        }

        return $baseStats;
    }

    /**
     * Obtener estadísticas globales del sistema
     */
    public static function getGlobalStats(): array
    {
        $contentTypeBreakdown = self::select('content_type', DB::raw('count(*) as total'))
            ->groupBy('content_type')
            ->pluck('total', 'content_type')
            ->toArray();

        return [
            'total_scans' => self::count(),
            'unique_scans' => self::where('is_unique_visit', true)->count(),
            'scans_today' => self::whereDate('accessed_at', today())->count(),
            'scans_this_week' => self::whereBetween('accessed_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'scans_this_month' => self::whereMonth('accessed_at', now()->month)->count(),
            'active_tokens' => NfcToken::where('is_active', true)->count(),
            'published_content' => DynamicContent::where('status', 'published')->where('is_active', true)->count(),
            'content_type_breakdown' => $contentTypeBreakdown,
            'content_by_type' => DynamicContent::select('type', DB::raw('count(*) as total'))
                ->where('status', 'published')
                ->where('is_active', true)
                ->groupBy('type')
                ->get(),
            'top_performing_content' => self::select('content_id', 'content_type', DB::raw('count(*) as total_views'))
                ->groupBy('content_id', 'content_type')
                ->orderByDesc('total_views')
                ->limit(10)
                ->get(),
        ];
    }

    /**
     * Obtener estadísticas para contenido específico (alias para getStatsForContent)
     */
    public static function getContentStats(string $contentId): array
    {
        return self::getStatsForContent($contentId);
    }

    /**
     * Detectar tipo de dispositivo básico
     */
    private static function detectDeviceType(?string $userAgent): ?string
    {
        if (! $userAgent) {
            return null;
        }

        if (preg_match('/iPad/', $userAgent)) {
            return 'tablet';
        }

        if (preg_match('/Mobile|Android|iPhone/', $userAgent)) {
            return 'mobile';
        }

        return 'desktop';
    }

    /**
     * Detectar navegador básico
     */
    private static function detectBrowser(?string $userAgent): ?string
    {
        if (! $userAgent) {
            return null;
        }

        if (preg_match('/Firefox/', $userAgent)) {
            return 'Firefox';
        }
        if (preg_match('/Chrome/', $userAgent)) {
            return 'Chrome';
        }
        if (preg_match('/Safari/', $userAgent) && ! preg_match('/Chrome/', $userAgent)) {
            return 'Safari';
        }
        if (preg_match('/Edge/', $userAgent)) {
            return 'Edge';
        }
        if (preg_match('/Opera/', $userAgent)) {
            return 'Opera';
        }

        return 'Other';
    }

    /**
     * Scope para analytics de hoy
     * @param mixed $query
     */
    public function scopeToday($query)
    {
        return $query->whereDate('accessed_at', today());
    }

    /**
     * Scope para analytics de esta semana
     * @param mixed $query
     */
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('accessed_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    /**
     * Scope para analytics de este mes
     * @param mixed $query
     */
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('accessed_at', now()->month);
    }

    /**
     * Scope para visitas únicas solamente
     * @param mixed $query
     */
    public function scopeUniqueVisits($query)
    {
        return $query->where('is_unique_visit', true);
    }
}
