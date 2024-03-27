<?php

namespace App\Filament\Admin\Resources\WebsitePageResource\Pages;

use App\Filament\Admin\Resources\WebsitePageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWebsitePage extends ListRecords
{
    protected static string $resource = WebsitePageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
