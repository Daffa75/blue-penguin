<?php

namespace App\Providers\Filament;

use App\Filament\Admin\Pages\Auth\CustomProfile;
use App\Filament\Auth\CustomLogin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

use Rupadana\ApiService\ApiServicePlugin;
use Althinect\FilamentSpatieRolesPermissions\FilamentSpatieRolesPermissionsPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->brandLogo(asset('assets/images/logo.png'))
            ->brandLogoHeight('2.5rem')
            ->favicon(asset('favicon.ico'))
            ->id('admin')
            ->path('admin')
            ->login(CustomLogin::class)
            ->profile(CustomProfile::class)
            ->sidebarCollapsibleOnDesktop()
            ->colors([
                'primary' => Color::Emerald,
            ])
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\\Filament\\Admin\\Resources')
            ->discoverClusters(in: app_path('Filament/Admin/Clusters'), for: 'App\\Filament\\Admin\\Clusters')
            ->discoverResources(in: app_path('Filament/FinalProject/Resources'), for: 'App\\Filament\\FinalProject\\Resources')
            ->discoverResources(in: app_path('Filament/Publication/Resources'), for: 'App\\Filament\\Publication\\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\\Filament\\Admin\\Pages')
            ->pages([])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->navigationGroups([
                NavigationGroup::make()
                    ->label(fn ():string => __('Website')),
                NavigationGroup::make()
                    ->label(fn ():string => __('Statistics')),
                NavigationGroup::make()
                    ->label(fn ():string => __('Content')),
                NavigationGroup::make()
                    ->label(fn ():string => __('Management')),
            ])
            ->plugins([
                ApiServicePlugin::make(),
                FilamentSpatieRolesPermissionsPlugin::make()

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
                // \App\Http\Middleware\Authenticate::class,
            ])
            ->viteTheme('resources/css/filament/admin/theme.css');
    }
}
