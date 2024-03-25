<?php

namespace App\Filament\Admin\Resources\EventResource\Api\Handlers;

use App\Filament\Resources\SettingResource;
use App\Filament\Admin\Resources\EventResource;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;

class DetailHandler extends Handlers
{
    public static string | null $uri = '/{id}';
    public static string | null $resource = EventResource::class;


    public function handler(Request $request)
    {
        $id = $request->route('id');

        $model = static::getEloquentQuery();
        $query = QueryBuilder::for($model)
            ->join('users', 'events.created_by', '=', 'users.id')
            ->select('events.*', 'users.name as author')
            ->where('events.id', $id) // Specify the table name or alias for the id column
            ->first();

        if (!$query) return static::sendNotFoundResponse();

        $transformer = static::getApiTransformer();

        return new $transformer($query);
    }
}
