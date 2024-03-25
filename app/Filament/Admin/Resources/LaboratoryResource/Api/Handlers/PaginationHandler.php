<?php
namespace App\Filament\Admin\Resources\LaboratoryResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use App\Filament\Admin\Resources\LaboratoryResource;
use App\Models\Laboratory;

class PaginationHandler extends Handlers {
    public static string | null $uri = '/';
    public static string | null $resource = LaboratoryResource::class;

    public function handler()
    {
        $model = static::getEloquentQuery();

        $query = QueryBuilder::for($model)
            ->with('media')
            ->allowedFields($model::$allowedFields ?? [])
            ->allowedSorts($model::$allowedSorts ?? [])
            ->allowedFilters($model::$allowedFilters ?? [])
            ->allowedIncludes($model::$allowedIncludes ?? null);

        // Selecting specific columns
        $query->select('id', 'name_en', 'name_id');

        // Paginate the results
        $pagination = $query->paginate(request()->query('per_page'))
                            ->appends(request()->query());
        
        // Modify each laboratory to include thumbnail
        $pagination->getCollection()->transform(function($lab) {
            $thumbnail = $lab->media->pluck('original_url')->first();
            $lab->thumbnail = $thumbnail;
            unset($lab->media);
            return $lab;
        });

        return $pagination;
    }
}
