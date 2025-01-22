<?php

namespace App\Filament\Admin\Resources\TeachingStaffResource\Api\Handlers;

use App\Filament\Resources\SettingResource;
use App\Filament\Admin\Resources\TeachingStaffResource;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;

class DetailHandler extends Handlers
{
    public static string | null $uri = '/{nip}';
    public static string | null $resource = TeachingStaffResource::class;

    public function handler(Request $request)
    {
        // Retrieve the `nip` parameter from the request
        $nip = $request->route('nip');

        if (!$nip) {
            return static::sendNotFoundResponse(); // Ensure `nip` is provided
        }

        // Get the base query
        $model = static::getEloquentQuery();

        // Build the query with necessary relationships and filter by `nip` in the related `lecturer` table
        $query = QueryBuilder::for($model)
            ->with(['lecturer.publications', 'role']) // Load the related models, including publications
            ->whereHas('lecturer', function ($q) use ($nip) {
                $q->where('nip', $nip); // Filter by `nip` in the `lecturers` table
            })
            ->first();

        // Check if the record exists
        if (!$query) {
            return static::sendNotFoundResponse();
        }

        // Get the API transformer and return the transformed response
        $transformer = static::getApiTransformer();

        return new $transformer($query);
    }
}
