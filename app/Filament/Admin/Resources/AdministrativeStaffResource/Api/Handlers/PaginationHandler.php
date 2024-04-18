<?php
namespace App\Filament\Admin\Resources\AdministrativeStaffResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use App\Filament\Admin\Resources\AdministrativeStaffResource;

class PaginationHandler extends Handlers {
    public static string | null $uri = '/';
    public static string | null $resource = AdministrativeStaffResource::class;


    public function handler()
    {
        $model = static::getEloquentQuery();

        $query = QueryBuilder::for($model)
        ->with('media')
        ->allowedFields($model::$allowedFields ?? [])
        ->allowedSorts($model::$allowedSorts ?? [])
        ->allowedFilters($model::$allowedFilters ?? [])
        ->allowedIncludes($model::$allowedIncludes ?? null)
        ->paginate(request()->query('per_page'))
        ->appends(request()->query());

        return static::getApiTransformer()::collection($query);
    }
}
