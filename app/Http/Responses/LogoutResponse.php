<?php

namespace App\Http\Responses;

use Filament\Auth\Http\Responses\Contracts\LogoutResponse as ResponseContract;
use Illuminate\Http\RedirectResponse;

class LogoutResponse implements ResponseContract
{
    public function toResponse($request): RedirectResponse
    {
        // change this to your desired route
        return redirect('/');
    }
}
