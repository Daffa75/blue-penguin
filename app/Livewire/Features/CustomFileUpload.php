<?php

namespace App\Livewire\Features;

use Livewire\Features\SupportFileUploads\FilePreviewController;
use Livewire\Features\SupportFileUploads\FileUploadController;
use Livewire\Features\SupportFileUploads\FileUploadSynth;
use Livewire\Features\SupportFileUploads\GenerateSignedUploadUrl;
use Livewire\Features\SupportFileUploads\MissingFileUploadsTraitException;

use function Livewire\on;
use Livewire\ComponentHook;
use Illuminate\Support\Facades\Route;
use Facades\Livewire\Features\SupportFileUploads\GenerateSignedUploadUrl as GenerateSignedUploadUrlFacade;
class CustomFileUpload extends ComponentHook
{
    static function provide()
    {
        if (app()->runningUnitTests()) {
            // Don't actually generate S3 signedUrls during testing.
            // Can't use ::partialMock because it's not available in older versions of Laravel.
            $mock = \Mockery::mock(GenerateSignedUploadUrl::class);
            $mock->makePartial()->shouldReceive('forS3')->andReturn([]);
            GenerateSignedUploadUrlFacade::swap($mock);
        }

        app('livewire')->propertySynthesizer([
            FileUploadSynth::class,
        ]);

        on('call', function ($component, $method, $params, $addEffect, $earlyReturn) {
            if ($method === '_startUpload') {
                if (! method_exists($component, $method)) {
                    throw new MissingFileUploadsTraitException($component);
                }
            }
        });

        Route::post('/livewire/upload-file', [FileUploadController::class, 'handle'])
            ->name('livewire.upload-file')
            ->middleware('web');

        Route::get('/livewire/preview-file/{filename}', [FilePreviewController::class, 'handle'])
            ->name('livewire.preview-file')
            ->middleware('web');
    }

}