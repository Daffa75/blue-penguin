<?php

namespace App\Filament\Admin\Resources\PostResource\Api\Handlers;

use App\Filament\Resources\SettingResource;
use App\Filament\Admin\Resources\PostResource;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;

class DetailHandler extends Handlers
{
    public static string | null $uri = '/{id}';
    public static string | null $resource = PostResource::class;


    public function handler(Request $request)
    {
        $id = $request->route('id');

        $model = static::getModel()::query();

        $query = QueryBuilder::for(
            $model
            ->where(static::getKeyName(), $id)
            ->where('status', 'published')
            ->where('published_at', '<=', now())
            ->with('media')
            ->with('created_by')
        )
            ->first();

        if (!$query) return static::sendNotFoundResponse();

        $transformer = static::getApiTransformer();
        
        return new $transformer($query);
    }
}
