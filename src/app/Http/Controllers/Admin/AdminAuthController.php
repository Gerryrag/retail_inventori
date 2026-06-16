<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (
            hash_equals(config('admin.username'), $credentials['username'])
            && hash_equals(config('admin.password'), $credentials['password'])
        ) {
            Auth::logout();

            $request->session()->regenerate();
            $request->session()->put('admin_authenticated', true);

            return redirect()->route('admin.dashboard');
        }

        return back()
            ->withInput($request->only('username'))
            ->with('status', 'Username atau password admin tidak sesuai.');
    }
}
