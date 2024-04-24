<?php

namespace App\Filament\Admin\Resources\EventResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use App\Filament\Admin\Resources\EventResource;

class PaginationHandler extends Handlers
{
    public static string | null $uri = '/';
    public static string | null $resource = EventResource::class;

    public static $allowedfilters = ['language', 'website'];
    public static $allowedSorts = ['date'];

    public function handler()
    {
        $model = static::getEloquentQuery();

        $query = QueryBuilder::for($model)
            ->join('users', 'events.created_by', '=', 'users.id')
            ->select('events.*', 'users.name as author')
            ->defaultSort('-date')
            ->allowedFields($model::$allowedFields ?? [])
            ->allowedSorts($model::$allowedSorts ?? [])
            ->allowedFilters($this::$allowedfilters ?? [])
            ->allowedIncludes($model::$allowedIncludes ?? null)
            ->paginate(request()->query('per_page'))
            ->appends(request()->query());

        return static::getApiTransformer()::collection($query);
    }
}
