<?php
namespace App\Filament\Admin\Resources\StructureResource\Api;

use Rupadana\ApiService\ApiService;
use App\Filament\Admin\Resources\StructureResource;
use Illuminate\Routing\Router;


class StructureApiService extends ApiService
{
    protected static string | null $resource = StructureResource::class;

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
