<?php

namespace App\Filament\Admin\Resources\ContentsResource\Pages;

use App\Filament\Admin\Resources\ContentsResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateContents extends CreateRecord
{
    protected static string $resource = ContentsResource::class;        

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
