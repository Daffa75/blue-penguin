<?php
namespace App\Filament\Admin\Resources\EventResource\Api;

use Rupadana\ApiService\ApiService;
use App\Filament\Admin\Resources\EventResource;
use Illuminate\Routing\Router;


class EventApiService extends ApiService
{
    protected static string | null $resource = EventResource::class;

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
