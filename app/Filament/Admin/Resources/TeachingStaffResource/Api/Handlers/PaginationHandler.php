<?php
namespace App\Filament\Admin\Resources\TeachingStaffResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use App\Filament\Admin\Resources\TeachingStaffResource;

class PaginationHandler extends Handlers {
    public static string | null $uri = '/';
    public static string | null $resource = TeachingStaffResource::class;


    public function handler()
    {
        $model = static::getEloquentQuery();

        $query = QueryBuilder::for($model)
        ->with('lecturer')
        ->with('role')
        ->allowedFields($model::$allowedFields ?? [])
        ->allowedSorts($model::$allowedSorts ?? [])
        ->allowedFilters($model::$allowedFilters ?? ['concentration'])
        ->allowedIncludes($model::$allowedIncludes ?? null)
        ->paginate(request()->query('per_page'))
        ->appends(request()->query());

        return static::getApiTransformer()::collection($query);
    }
}
