<?php

namespace App\Providers\Filament;

use App\Filament\Admin\Pages\Auth\CustomProfile;
use App\Filament\Admin\Resources\DepartmentEventResource;
use App\Filament\Auth\CustomLogin;
use App\Filament\Widgets\AccountOverviewCustom;
use App\Filament\Widgets\PublicationChart;
use App\Filament\Widgets\PublicationLineChart;
use App\Filament\Publication\Pages\Dashboard;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

use Rupadana\ApiService\ApiServicePlugin;
use Althinect\FilamentSpatieRolesPermissions\FilamentSpatieRolesPermissionsPlugin;
use Saade\FilamentFullCalendar\FilamentFullCalendarPlugin;

class LecturerPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->brandLogo(asset('assets/images/unhas-logo.png'))
            ->brandLogoHeight('2.5rem')
            ->favicon(asset('favicon.ico'))
            ->id('lecturer')
            ->path('dosen')
            ->login(CustomLogin::class)
            ->profile(CustomProfile::class)
            ->colors([
                'primary' => Color::Sky,
            ])
            ->discoverResources(in: app_path('Filament/Lecturer/Resources'), for: 'App\\Filament\\Lecturer\\Resources')
            ->discoverPages(in: app_path('Filament/Lecturer/Pages'), for: 'App\\Filament\\Lecturer\\Pages')
            ->resources([
                \App\Filament\Admin\Resources\DepartmentEventResource::class,
                \App\Filament\Admin\Resources\InternshipResource::class,
                \App\Filament\Admin\Resources\InventarisResource::class,
            ])
            ->sidebarCollapsibleOnDesktop()
            ->plugins([
                ApiServicePlugin::make(),
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
            ])
            ->viteTheme('resources/css/filament/admin/theme.css');
    }
}
