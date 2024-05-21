<?php

namespace App\Providers;

use App\Livewire\Features\CustomGenerateSignedUploadUrl;
use Illuminate\Support\Facades\Route;
use Livewire\ComponentHookRegistry;
use Livewire\Features\SupportFileUploads\GenerateSignedUploadUrl;
use Livewire\Livewire;
use Livewire\LivewireServiceProvider;

/**
 * This custom service provider overwrites the default LivewireServiceProvider.
 * It is required so that the routes are registered inside a tenant's namespace.
 *
 * <ul>
 * <li>Add <em>livewire/livewire</em> to the <em>dont-discover</em> section inside your <em>compoers.json</em></li>
 * <li>Call CustomLivewireServiceProvider::registerLivewireRoutes() in your <em>routes/web.php</em></li>
 * </ul>
 */
class CustomLivewireServiceProvider extends LivewireServiceProvider
{
    /**
     * Register the default Livewire routes.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(GenerateSignedUploadUrl::class, CustomGenerateSignedUploadUrl::class);
        $this->bootMechanisms();
        $this->bootFeatures();
        Livewire::setUpdateRoute(function ($handle) {
            return Route::post('/siminformatika/livewire/update', $handle);
        });

        Livewire::setScriptRoute(function ($handle) {
            return config('app.debug')
                ? Route::get('/siminformatika/livewire/livewire.js', $handle)
                : Route::get('/siminformatika/livewire/livewire.min.js', $handle);
        });
    }

    protected function bootFeatures()
    {
        foreach([
                    \Livewire\Features\SupportWireModelingNestedComponents\SupportWireModelingNestedComponents::class,
                    \Livewire\Features\SupportMultipleRootElementDetection\SupportMultipleRootElementDetection::class,
                    \Livewire\Features\SupportDisablingBackButtonCache\SupportDisablingBackButtonCache::class,
                    \Livewire\Features\SupportNestedComponentListeners\SupportNestedComponentListeners::class,
                    \Livewire\Features\SupportMorphAwareIfStatement\SupportMorphAwareIfStatement::class,
                    \Livewire\Features\SupportAutoInjectedAssets\SupportAutoInjectedAssets::class,
                    \Livewire\Features\SupportComputed\SupportLegacyComputedPropertySyntax::class,
                    \Livewire\Features\SupportNestingComponents\SupportNestingComponents::class,
                    \Livewire\Features\SupportScriptsAndAssets\SupportScriptsAndAssets::class,
                    \Livewire\Features\SupportBladeAttributes\SupportBladeAttributes::class,
                    \Livewire\Features\SupportConsoleCommands\SupportConsoleCommands::class,
                    \Livewire\Features\SupportPageComponents\SupportPageComponents::class,
                    \Livewire\Features\SupportReactiveProps\SupportReactiveProps::class,
                    \Livewire\Features\SupportFileDownloads\SupportFileDownloads::class,
                    \Livewire\Features\SupportJsEvaluation\SupportJsEvaluation::class,
                    \Livewire\Features\SupportQueryString\SupportQueryString::class,
                    \App\Livewire\Features\CustomFileUpload::class,
                    \Livewire\Features\SupportTeleporting\SupportTeleporting::class,
                    \Livewire\Features\SupportLazyLoading\SupportLazyLoading::class,
                    \Livewire\Features\SupportFormObjects\SupportFormObjects::class,
                    \Livewire\Features\SupportAttributes\SupportAttributes::class,
                    \Livewire\Features\SupportPagination\SupportPagination::class,
                    \Livewire\Features\SupportValidation\SupportValidation::class,
                    \Livewire\Features\SupportRedirects\SupportRedirects::class,
                    \Livewire\Features\SupportStreaming\SupportStreaming::class,
                    \Livewire\Features\SupportNavigate\SupportNavigate::class,
                    \Livewire\Features\SupportEntangle\SupportEntangle::class,
                    \Livewire\Features\SupportLocales\SupportLocales::class,
                    \Livewire\Features\SupportTesting\SupportTesting::class,
                    \Livewire\Features\SupportModels\SupportModels::class,
                    \Livewire\Features\SupportEvents\SupportEvents::class,

                    // Some features we want to have priority over others...
                    \Livewire\Features\SupportLifecycleHooks\SupportLifecycleHooks::class,
                    \Livewire\Features\SupportLegacyModels\SupportLegacyModels::class,
                    \Livewire\Features\SupportWireables\SupportWireables::class,
                ] as $feature) {
            app('livewire')->componentHook($feature);
        }

        ComponentHookRegistry::boot();
    }
}
