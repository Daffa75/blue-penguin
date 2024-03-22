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

    public static $allowedfilters = ['language'];
    public static $allowedSorts = ['date'];

    public function handler()
    {
        $model = static::getEloquentQuery();

        $query = QueryBuilder::for($model)
            ->with('media')
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
