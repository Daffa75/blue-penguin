<?php

namespace App\Providers;

use BezhanSalleh\PanelSwitch\PanelSwitch;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse as LogoutResponseContract;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use App\Filament\Auth\CustomLoginResponse;
use App\Filament\Auth\CustomLogoutResponse;
use Filament\Http\Responses\Auth\Contracts\LoginResponse as LoginResponseContract;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;

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
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        $this->app->bind(LoginResponseContract::class, CustomLoginResponse::class);
        $this->app->bind(LogoutResponseContract::class, CustomLogoutResponse::class);

        FilamentAsset::register([
            Js::make('custom-script', __DIR__ . '/../../resources/js/custom.js')->loadedOnRequest(),
        ]);
        
        FilamentColor::register([
            'violet' => Color::hex('#6B33AF'),
            'teal' => Color::Teal,
            'sky' => Color::Sky
        ]);

        PanelSwitch::configureUsing(function (PanelSwitch $panelSwitch) {
            $panelSwitch
                ->modalWidth('lg')
                ->slideOver()
                ->labels([
                    'lecturer' => __('Lecturer'),
                    'student' => __('Student'),
                ])
                ->icons([
                    'admin' => 'heroicon-m-users',
                    'lecturer' => 'heroicon-m-document-text',
                    'student' =>'phosphor-article-fill',
                ])
                ->excludes(fn () => (auth()->user()->role !== '0') ? ['admin'] : []);
        });

        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->visible(outsidePanels: true)
                ->flags([
                    'en' => asset('assets/images/us.svg'),
                    'id' => asset('assets/images/id.svg'),
                ])
                ->locales(['en', 'id']); // also accepts a closure
        });
    }
}