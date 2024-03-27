<?php

namespace App\Filament\Admin\Resources\WebsitePageResource\Pages;

use App\Filament\Admin\Resources\WebsitePageResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateWebsitePage extends CreateRecord
{
    protected static string $resource = WebsitePageResource::class;        

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
