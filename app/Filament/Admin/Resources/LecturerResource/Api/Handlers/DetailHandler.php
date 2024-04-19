<?php

namespace App\Filament\Admin\Resources\LecturerResource\Api\Handlers;

use App\Filament\Resources\SettingResource;
use App\Filament\Admin\Resources\LecturerResource;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;

class DetailHandler extends Handlers
{
    public static string | null $uri = '/{nip}';
    public static string | null $resource = LecturerResource::class;

    public function handler(Request $request)
    {
        $nip = $request->route('nip');
        
        $model = static::getEloquentQuery();

        $query = QueryBuilder::for($model)
            ->where('nip', $nip)
            ->with(['publications' => function ($query) use ($request) {
                // Filter publications by type if the 'type' query parameter is provided
                if ($request->has('type')) {
                    $query->where('type', $request->input('type'));
                }
            }])
            ->first();

        if (!$query) {
            return static::sendNotFoundResponse();
        }

        $transformer = static::getApiTransformer();

        // Paginate the publications if 'paginate' query parameter is provided
        if ($request->has('paginate')) {
            $publicationsQuery = $query->publications()->getQuery();
            // Apply type filter to the publications query if 'type' query parameter is provided
            if ($request->has('type')) {
                $publicationsQuery->where('type', $request->input('type'));
            }
            $publications = $publicationsQuery->paginate($request->input('paginate'));
        } else {
            // Retrieve publications without pagination
            $publications = $query->publications;
        }

        // Append the paginated publications to the query result
        $query->setRelation('publications', $publications);

        return new $transformer($query);
    }
}


