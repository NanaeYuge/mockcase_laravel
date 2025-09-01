<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Support\Facades\Auth;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = Auth::user();

        if (empty($user->address) || empty($user->postal_code) || empty($user->name)) {
            return redirect()->route('profile.edit');
        }

        return redirect()->intended(route('/'));
    }
}