<?php
namespace App\Filament\Admin\Resources\LecturerResource\Api;

use Rupadana\ApiService\ApiService;
use App\Filament\Admin\Resources\LecturerResource;
use Illuminate\Routing\Router;


class LecturerApiService extends ApiService
{
    protected static string | null $resource = LecturerResource::class;

    public static function handlers() : array
    {
        return [
            Handlers\CreateHandler::class,
            Handlers\UpdateHandler::class,
            Handlers\DeleteHandler::class,
            Handlers\DetailHandler::class
        ];

    }
}
