<?php

namespace App\Filament\Admin\Resources\PostResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use App\Filament\Admin\Resources\PostResource;

class PaginationHandler extends Handlers
{
    public static string | null $uri = '/';
    public static string | null $resource = PostResource::class;

    public static $allowedfilters = ['language', 'website'];
    public static $allowedSorts = ['published_at'];


    public function handler()
    {
        $model = static::getEloquentQuery();

        $query = QueryBuilder::for($model)
            ->join('users', 'posts.created_by', '=', 'users.id')
            ->select('posts.*', 'users.name as author')
            ->where('status', 'published')
            ->where('published_at', '<=', now())
            ->defaultSort('-published_at')
            ->allowedFields($model::$allowedFields ?? [])
            ->allowedSorts($model::$allowedSorts ?? [])
            ->allowedFilters($this::$allowedfilters ?? [])
            ->allowedIncludes($model::$allowedIncludes ?? null)
            ->paginate(10)
            ->appends(request()->query());

        return static::getApiTransformer()::collection($query);
    }
}
