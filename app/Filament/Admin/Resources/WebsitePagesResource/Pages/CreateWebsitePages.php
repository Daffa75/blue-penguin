<?php

namespace App\Filament\Admin\Resources\WebsitePagesResource\Pages;

use App\Filament\Admin\Resources\WebsitePagesResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateWebsitePages extends CreateRecord
{
    protected static string $resource = WebsitePagesResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
