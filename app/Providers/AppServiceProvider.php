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
use App\Observers\ContentEventObserver;
use App\Observers\ContentTouristObserver;
use App\Observers\ContentBusStopObserver;
use App\Observers\DynamicContentObserver;
use App\Observers\NfcTokenObserver;
use App\Services\GiftContentService;
use App\Services\ProfileContentService;
use App\Services\BusinessContentService;
use App\Services\EventContentService;
use App\Services\TouristContentService;
use App\Services\BusStopContentService;
use App\Services\ContentCacheService;
use App\Services\ContentObservabilityService;
use App\Policies\ContentEventPolicy;
use App\Policies\ContentTouristPolicy;
use App\Policies\ContentBusStopPolicy;
use App\Models\ContentEvent;
use App\Models\ContentTourist;
use App\Models\BusStop;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Gate;
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

        $this->app->singleton(ProfileContentService::class, function () {
            return new ProfileContentService();
        });

        $this->app->singleton(BusinessContentService::class, function () {
            return new BusinessContentService();
        });

        $this->app->singleton(EventContentService::class, function () {
            return new EventContentService();
        });

        $this->app->singleton(TouristContentService::class, function () {
            return new TouristContentService();
        });

        $this->app->singleton(BusStopContentService::class, function () {
            return new BusStopContentService();
        });

        $this->app->singleton(ContentCacheService::class, function () {
            return new ContentCacheService();
        });

        $this->app->singleton(ContentObservabilityService::class, function () {
            return new ContentObservabilityService();
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
        ContentEvent::observe(ContentEventObserver::class);
        ContentTourist::observe(ContentTouristObserver::class);
        // BusStop::observe(ContentBusStopObserver::class); // TODO: Fix model name mismatch

        // 🔐 Register content type specific policies
        Gate::policy(ContentEvent::class, ContentEventPolicy::class);
        Gate::policy(ContentTourist::class, ContentTouristPolicy::class);
        // Gate::policy(BusStop::class, ContentBusStopPolicy::class); // TODO: Fix model name mismatch
    }
}
