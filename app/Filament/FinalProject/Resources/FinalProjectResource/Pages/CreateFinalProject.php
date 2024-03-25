<?php

namespace App\Filament\FinalProject\Resources\FinalProjectResource\Pages;

use App\Filament\FinalProject\Resources\FinalProjectResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;

class CreateFinalProject extends CreateRecord
{
    protected static string $resource = FinalProjectResource::class;
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-ellipsis';
    public function getTitle(): string|Htmlable
    {
        return (__('Make Final Project'));
    }
}
