<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleController extends Controller
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Throwable $exception) {
            Log::error('Google login failed', [
                'message' => $exception->getMessage(),
                'exception' => $exception::class,
            ]);

            $message = 'Login Google belum berhasil. Periksa Client ID, Client Secret, dan Redirect URI.';

            if (config('app.debug')) {
                $message .= ' Detail: '.$exception->getMessage();
            }

            return redirect()
                ->route('login')
                ->with('status', $message);
        }

        $user = User::updateOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'name' => $googleUser->getName() ?: $googleUser->getNickname() ?: 'Retail User',
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'role' => 'customer',
                'email_verified_at' => now(),
                'password' => Str::random(40),
            ],
        );

        Auth::login($user, remember: true);

        return redirect()->intended(route('home'));
    }

    public function logout(): RedirectResponse
    {
        Auth::logout();

        request()->session()->forget('admin_authenticated');
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('home');
    }
}
