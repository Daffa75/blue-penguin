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

    public static $allowedfilters = ['language'];
    public static $allowedSorts = ['published_at'];


    public function handler()
    {
        $model = static::getEloquentQuery();

        $query = QueryBuilder::for($model)
            // ->with('media')
            // ->select('slug', 'title', 'article', 'language', 'published_at')
            ->where('status', 'published')
            ->where('published_at', '<=', now())
            ->defaultSort('-published_at')
            ->allowedFields($model::$allowedFields ?? [])
            ->allowedSorts($model::$allowedSorts ?? [])
            ->allowedFilters($this::$allowedfilters ?? [])
            ->allowedIncludes($model::$allowedIncludes ?? null)
            ->paginate(10)
            ->appends(request()->query());

        foreach ($query as $post) {
            $thumbnail = $post->media->pluck('original_url')->toArray();
            $post->thumbnail = $thumbnail;
            unset($post->media);
            unset($post->id);
            unset($post->created_by);
            unset($post->updated_by);
            unset($post->created_at);
            unset($post->updated_at);
            unset($post->status);
            unset($post->id);
        }

        return static::getApiTransformer()::collection($query);
    }
}
