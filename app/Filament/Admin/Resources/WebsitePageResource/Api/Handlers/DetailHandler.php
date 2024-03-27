<?php

namespace App\Filament\Admin\Resources\WebsitePageResource\Api\Handlers;

use App\Filament\Resources\SettingResource;
use App\Filament\Admin\Resources\WebsitePageResource;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;

class DetailHandler extends Handlers
{
    public static string | null $uri = '/{slug}';
    public static string | null $resource = WebsitePageResource::class;

    public function handler(Request $request)
    {
        $slug = $request->route('slug');
        $lang = $request->query('lang', 'id'); // Default language is English if not provided

        // Your existing code to fetch data based on $slug
        $model = static::getModel()::query();
        $query = QueryBuilder::for($model->where('slug', $slug))->first();

        if (!$query) {
            return static::sendNotFoundResponse();
        }

        // Determine which content field to use based on the language
        $contentField = 'content_' . $lang;

        // Check if the specified language content field exists in the retrieved data
        if (!isset($query->$contentField)) {
            // If the specified language content field does not exist, return an error response
            return response()->json(['error' => 'Content not available in the specified language.'], 400);
        }

        // Retrieve the page name
        $pageName = $query->page;

        // Retrieve the content in the specified language
        $content = $query->$contentField;

        // Construct the response data structure including both page name and content
        $responseData = [
            'page' => $pageName,
            'content' => $content
        ];

        return $responseData;
    }
}
