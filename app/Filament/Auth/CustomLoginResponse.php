<?php

namespace App\Filament\Auth;

use Filament\Facades\Filament;
use Filament\Http\Responses\Auth\Contracts\LoginResponse as BaseLogin;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

class CustomLoginResponse implements BaseLogin
{
    public function toResponse($request): RedirectResponse | Redirector
    {
        return redirect()->to(Filament::getUrl());
    }
}