<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->colors([
                'primary' => [
                    50 => '240, 249, 255',
                    100 => '224, 242, 254',
                    200 => '186, 230, 253',
                    300 => '125, 211, 252',
                    400 => '74, 144, 226',
                    500 => '74, 144, 226',
                    600 => '59, 115, 181',
                    700 => '47, 92, 145',
                    800 => '37, 73, 116',
                    900 => '42, 52, 65',
                    950 => '42, 52, 65',
                ],
                'secondary' => [
                    50 => '240, 255, 248',
                    100 => '220, 255, 237',
                    200 => '187, 255, 208',
                    300 => '134, 255, 172',
                    400 => '0, 255, 127',
                    500 => '0, 255, 127',
                    600 => '0, 204, 102',
                    700 => '0, 153, 76',
                    800 => '0, 102, 51',
                    900 => '0, 76, 38',
                    950 => '0, 51, 25',
                ],
                'success' => [
                    50 => '240, 255, 248',
                    100 => '220, 255, 237',
                    200 => '187, 255, 208',
                    300 => '134, 255, 172',
                    400 => '50, 255, 50',
                    500 => '50, 255, 50',
                    600 => '40, 204, 40',
                    700 => '30, 153, 30',
                    800 => '20, 102, 20',
                    900 => '15, 76, 15',
                    950 => '10, 51, 10',
                ],
                'warning' => Color::Amber,
                'danger' => Color::Red,
                'info' => [
                    50 => '240, 249, 255',
                    100 => '224, 242, 254',
                    200 => '186, 230, 253',
                    300 => '125, 211, 252',
                    400 => '74, 144, 226',
                    500 => '74, 144, 226',
                    600 => '59, 115, 181',
                    700 => '47, 92, 145',
                    800 => '37, 73, 116',
                    900 => '42, 52, 65',
                    950 => '42, 52, 65',
                ],
            ])
            ->authGuard('web')
            ->authPasswordBroker('users')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->brandName('KRAFTDO NFC')
            ->brandLogo(asset('images/kraftdo-logo.svg'))
            ->brandLogoHeight('2rem')
            ->favicon(asset('favicon.ico'))
            ->font('Inter')
            ->darkMode(true)
            ->maxContentWidth('full')
            ->navigationGroups([
                'Contenido NFC',
                'Usuarios & Roles',
                'Analytics',
                'Configuración'
            ]);
    }
}
