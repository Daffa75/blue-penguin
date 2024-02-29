<?php

namespace App\Filament\Admin\Resources\WebsitePagesResource\Pages;

use App\Filament\Admin\Resources\WebsitePagesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWebsitePages extends ListRecords
{
    protected static string $resource = WebsitePagesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
