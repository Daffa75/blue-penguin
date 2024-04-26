<?php

namespace App\Providers\Filament;

use App\Filament\Admin\Pages\Auth\CustomProfile;
use App\Filament\Auth\CustomLogin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
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

use App\Filament\Admin\Resources\InventarisResource;
use App\Filament\Lecturer\Resources\FinalProjectResource;
use App\Filament\Lecturer\Resources\FinalProjectS2Resource;

use Rupadana\ApiService\ApiServicePlugin;
use Althinect\FilamentSpatieRolesPermissions\FilamentSpatieRolesPermissionsPlugin;
use Saade\FilamentFullCalendar\FilamentFullCalendarPlugin;

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
            ->discoverResources(in: app_path('Filament/Lecturer/Resources'), for: 'App\\Filament\\Lecturer\\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\\Filament\\Admin\\Pages')
            ->pages([])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->navigationGroups([
                NavigationGroup::make()
                    ->label(fn ():string => __('Statistics')),
                NavigationGroup::make()
                    ->label(fn ():string => __('Website')),
                NavigationGroup::make()
                    ->label(fn ():string => __('Content')),
                NavigationGroup::make()
                    ->label(fn ():string => __('Roles and Permissions')),
                NavigationGroup::make()
                    ->label(fn ():string => __('Management')),
            ])
            ->navigationItems([
                // For Content Group
                NavigationItem::make(fn (): string => __('Final Project Bachelor'))
                    ->url(fn (): string => FinalProjectResource::getUrl())
                    ->group(fn (): string => __('Content')),
                NavigationItem::make(fn (): string => __('Final Project Master'))
                    ->url(fn (): string => FinalProjectS2Resource::getUrl())
                    ->group(fn (): string => __('Content')),
            ])
            ->plugins([
                ApiServicePlugin::make(),
                FilamentSpatieRolesPermissionsPlugin::make(),
                FilamentFullCalendarPlugin::make(),

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
