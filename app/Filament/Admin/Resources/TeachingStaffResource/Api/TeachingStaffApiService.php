<?php
namespace App\Filament\Admin\Resources\TeachingStaffResource\Api;

use Rupadana\ApiService\ApiService;
use App\Filament\Admin\Resources\TeachingStaffResource;
use Illuminate\Routing\Router;


class TeachingStaffApiService extends ApiService
{
    protected static string | null $resource = TeachingStaffResource::class;

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
