<?php

namespace App\Filament\Admin\Resources\WebsitePageResource\Pages;

use App\Filament\Admin\Resources\WebsitePageResource;
use Filament\Resources\Pages\EditRecord;

class EditWebsitePage extends EditRecord
{
    protected static string $resource = WebsitePageResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}
