<?php

namespace App\Filament\Admin\Resources\ContentsResource\Pages;

use App\Filament\Admin\Resources\ContentsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListContents extends ListRecords
{
    protected static string $resource = ContentsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
