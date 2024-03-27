<?php
namespace App\Filament\Admin\Resources\ContentsResource\Api;

use Rupadana\ApiService\ApiService;
use App\Filament\Admin\Resources\ContentsResource;
use Illuminate\Routing\Router;


class ContentsApiService extends ApiService
{
    protected static string | null $resource = ContentsResource::class;

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
