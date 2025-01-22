<?php

namespace App\Livewire\Features;

use Illuminate\Support\Facades\URL;
use Livewire\Features\SupportFileUploads\FileUploadConfiguration;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use function Livewire\invade;

class CustomGenerateSignedUploadUrl
{
    public function forLocal()
    {
        $url = URL::temporarySignedRoute(
            'livewire.upload-file',
            now()->addMinutes(FileUploadConfiguration::maxUploadTime()),
            absolute: false
        );

        return 'https://eng.unhas.ac.id/siminformatika' . $url;
    }

    public function forS3($file, $visibility = 'private')
    {
        $driver = FileUploadConfiguration::storage()->getDriver();

        // Flysystem V2+ doesn't allow direct access to adapter, so we need to invade instead.
        $adapter = invade($driver)->adapter;

        // Flysystem V2+ doesn't allow direct access to client, so we need to invade instead.
        $client = invade($adapter)->client;

        // Flysystem V2+ doesn't allow direct access to bucket, so we need to invade instead.
        $bucket = invade($adapter)->bucket;

        $fileType = $file->getMimeType();
        $fileHashName = TemporaryUploadedFile::generateHashNameWithOriginalNameEmbedded($file);
        $path = FileUploadConfiguration::path($fileHashName);

        $command = $client->getCommand('putObject', array_filter([
            'Bucket' => $bucket,
            'Key' => $path,
            'ACL' => $visibility,
            'ContentType' => $fileType ?: 'application/octet-stream',
            'CacheControl' => null,
            'Expires' => null,
        ]));

        $signedRequest = $client->createPresignedRequest(
            $command,
            '+' . FileUploadConfiguration::maxUploadTime() . ' minutes'
        );

        return [
            'path' => $fileHashName,
            'url' => (string) $signedRequest->getUri(),
            'headers' => $this->headers($signedRequest, $fileType),
        ];
    }

    protected function headers($signedRequest, $fileType)
    {
        return array_merge(
            $signedRequest->getHeaders(),
            [
                'Content-Type' => $fileType ?: 'application/octet-stream'
            ]
        );
    }
}