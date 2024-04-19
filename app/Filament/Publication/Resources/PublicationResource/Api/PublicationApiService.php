<?php
namespace App\Filament\Publication\Resources\PublicationResource\Api;

use Rupadana\ApiService\ApiService;
use App\Filament\Publication\Resources\PublicationResource;
use Illuminate\Routing\Router;


class PublicationApiService extends ApiService
{
    protected static string | null $resource = PublicationResource::class;

    public static function handlers() : array
    {
        return [
            Handlers\CreateHandler::class,
            Handlers\UpdateHandler::class,
            Handlers\DeleteHandler::class,
            Handlers\PaginationHandler::class,
            Handlers\DetailHandler::class
        ];

    }
}
