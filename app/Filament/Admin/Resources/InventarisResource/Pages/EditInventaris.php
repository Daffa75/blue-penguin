<?php

namespace App\Filament\Admin\Resources\InventarisResource\Pages;

use App\Filament\Admin\Resources\InventarisResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInventaris extends EditRecord
{
    protected static string $resource = InventarisResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
