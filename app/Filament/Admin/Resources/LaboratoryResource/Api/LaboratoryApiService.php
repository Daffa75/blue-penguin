<?php
namespace App\Filament\Admin\Resources\LaboratoryResource\Api;

use Rupadana\ApiService\ApiService;
use App\Filament\Admin\Resources\LaboratoryResource;
use Illuminate\Routing\Router;


class LaboratoryApiService extends ApiService
{
    protected static string | null $resource = LaboratoryResource::class;

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
