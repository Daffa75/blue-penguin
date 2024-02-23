<?php

namespace App\Filament\Admin\Resources\ContentsResource\Pages;

use App\Filament\Admin\Resources\ContentsResource;
use Filament\Resources\Pages\EditRecord;

class EditContents extends EditRecord
{
    protected static string $resource = ContentsResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
