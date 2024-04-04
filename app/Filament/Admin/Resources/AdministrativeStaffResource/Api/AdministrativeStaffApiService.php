<?php
namespace App\Filament\Admin\Resources\AdministrativeStaffResource\Api;

use Rupadana\ApiService\ApiService;
use App\Filament\Admin\Resources\AdministrativeStaffResource;
use Illuminate\Routing\Router;


class AdministrativeStaffApiService extends ApiService
{
    protected static string | null $resource = AdministrativeStaffResource::class;

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
