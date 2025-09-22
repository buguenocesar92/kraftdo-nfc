<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use App\Models\NfcToken;
use App\Models\DynamicContent;
use App\Models\ContentMultimedia;
use App\Models\ContentGift;
use App\Models\ContentProfile;
use App\Observers\NfcTokenObserver;
use App\Observers\DynamicContentObserver;
use App\Observers\ContentMultimediaObserver;
use App\Observers\ContentProfileObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force clean URLs without index.php globally
        URL::forceRootUrl(config('app.url'));
        URL::forceScheme(parse_url(config('app.url'), PHP_URL_SCHEME) ?: 'https');
        
        // 🚀 Registrar observers para invalidación automática de cache
        NfcToken::observe(NfcTokenObserver::class);
        DynamicContent::observe(DynamicContentObserver::class);
        ContentMultimedia::observe(ContentMultimediaObserver::class);
        
        // Observer genérico para otros tipos de contenido
        ContentGift::observe(DynamicContentObserver::class);
        ContentProfile::observe(ContentProfileObserver::class);
    }
}
