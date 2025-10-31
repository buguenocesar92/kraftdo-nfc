<?php

namespace App\Providers;

use App\Models\ContentGift;
use App\Models\ContentMultimedia;
use App\Models\ContentProfile;
use App\Models\DynamicContent;
use App\Models\NfcToken;
use App\Observers\ContentGiftObserver;
use App\Observers\ContentMultimediaObserver;
use App\Observers\ContentProfileObserver;
use App\Observers\DynamicContentObserver;
use App\Observers\NfcTokenObserver;
use App\Services\GiftContentService;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register services
        $this->app->singleton(GiftContentService::class, function () {
            return new GiftContentService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force clean URLs without index.php globally
        URL::forceRootUrl(config('app.url'));
        URL::forceScheme(parse_url(config('app.url'), PHP_URL_SCHEME) ?: 'https');

        // Configure route model binding
        Route::model('token', NfcToken::class);

        // 🚀 Registrar observers para invalidación automática de cache
        NfcToken::observe(NfcTokenObserver::class);
        DynamicContent::observe(DynamicContentObserver::class);
        ContentMultimedia::observe(ContentMultimediaObserver::class);

        // Observers específicos para tipos de contenido
        ContentGift::observe(ContentGiftObserver::class);
        ContentProfile::observe(ContentProfileObserver::class);
    }
}
