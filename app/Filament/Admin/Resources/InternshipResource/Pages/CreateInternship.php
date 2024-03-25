<?php

namespace App\Filament\Admin\Resources\InternshipResource\Pages;

use App\Filament\Admin\Resources\InternshipResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateInternship extends CreateRecord
{
    protected static string $resource = InternshipResource::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-ellipsis';
}
