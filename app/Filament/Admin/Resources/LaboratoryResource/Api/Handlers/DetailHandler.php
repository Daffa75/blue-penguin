<?php

namespace App\Filament\Admin\Resources\LaboratoryResource\Api\Handlers;

use App\Filament\Resources\SettingResource;
use App\Filament\Admin\Resources\LaboratoryResource;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;

class DetailHandler extends Handlers
{
    public static string | null $uri = '/{id}';
    public static string | null $resource = LaboratoryResource::class;


    public function handler(Request $request)
    {
        $id = $request->route('id');
        
        $model = static::getEloquentQuery();

        $query = QueryBuilder::for(
            $model->where(static::getKeyName(), $id)
            ->with('media')
            ->with('students')
            ->with('lecturers')
        )
            ->first();

        if (!$query) return static::sendNotFoundResponse();

        $transformer = static::getApiTransformer();

        return new $transformer($query);
    }
}
