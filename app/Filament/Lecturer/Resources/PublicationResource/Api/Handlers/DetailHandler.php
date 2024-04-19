<?php

namespace App\Filament\Lecturer\Resources\PublicationResource\Api\Handlers;

use App\Filament\Resources\SettingResource;
use App\Filament\Lecturer\Resources\PublicationResource;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;

class DetailHandler extends Handlers
{
    public static string | null $uri = '/{id}';
    public static string | null $resource = PublicationResource::class;


    public function handler(Request $request)
    {
        $id = $request->route('id');
        
        $model = static::getEloquentQuery();

        $query = QueryBuilder::for(
            $model->where(static::getKeyName(), $id)
        )
            ->with('lecturers')
            ->first();

        if (!$query) return static::sendNotFoundResponse();

        $transformer = static::getApiTransformer();

        return new $transformer($query);
    }
}
