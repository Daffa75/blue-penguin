<?php
namespace App\Filament\Admin\Resources\WebsitePageResource\Api;

use Rupadana\ApiService\ApiService;
use App\Filament\Admin\Resources\WebsitePageResource;
use Illuminate\Routing\Router;


class WebsitePageApiService extends ApiService
{
    protected static string | null $resource = WebsitePageResource::class;

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
