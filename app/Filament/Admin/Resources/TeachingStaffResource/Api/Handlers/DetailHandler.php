<?php

namespace App\Filament\Admin\Resources\TeachingStaffResource\Api\Handlers;

use App\Filament\Resources\SettingResource;
use App\Filament\Admin\Resources\TeachingStaffResource;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;

class DetailHandler extends Handlers
{
    public static string | null $uri = '/{nip}';
    public static string | null $resource = TeachingStaffResource::class;


    public function handler(Request $request)
    {        
        $model = static::getEloquentQuery();

        $query = QueryBuilder::for(
            $model->with('lecturer')
            ->with('role')
        )
            ->first();

        if (!$query) return static::sendNotFoundResponse();

        $transformer = static::getApiTransformer();

        return new $transformer($query);
    }
}
