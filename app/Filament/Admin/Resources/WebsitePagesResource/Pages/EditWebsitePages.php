<?php

namespace App\Filament\Admin\Resources\WebsitePagesResource\Pages;

use App\Filament\Admin\Resources\WebsitePagesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWebsitePages extends EditRecord
{
    protected static string $resource = WebsitePagesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
