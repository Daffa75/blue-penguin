<?php
namespace App\Filament\Admin\Resources\StructureResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use App\Filament\Admin\Resources\StructureResource;

class PaginationHandler extends Handlers {
    public static string | null $uri = '/';
    public static string | null $resource = StructureResource::class;


    public function handler()
    {
        $model = static::getEloquentQuery();

        $query = QueryBuilder::for($model)
        ->with('semester.modules')
        ->with('media')
        ->allowedFields($model::$allowedFields ?? [])
        ->allowedSorts($model::$allowedSorts ?? [])
        ->allowedFilters(['language', 'website'])
        ->allowedIncludes($model::$allowedIncludes ?? null)
        ->paginate(request()->query('per_page'))
        ->appends(request()->query());

        return static::getApiTransformer()::collection($query);
    }
}
