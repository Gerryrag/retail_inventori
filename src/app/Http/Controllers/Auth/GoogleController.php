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

        $email = Str::lower((string) $googleUser->getEmail());

        if (! $email || ! in_array($email, config('admin.emails', []), true)) {
            return redirect()
                ->route('login')
                ->with('status', 'Email Google ini tidak terdaftar sebagai admin. Tambahkan email admin ke ADMIN_EMAILS.');
        }

        $user = User::firstOrNew(['email' => $email]);
        $user->fill([
            'name' => $googleUser->getName() ?: $googleUser->getNickname() ?: 'Admin',
            'google_id' => $googleUser->getId(),
            'avatar' => $googleUser->getAvatar(),
            'email_verified_at' => now(),
        ]);

        if (! $user->exists) {
            $user->password = Str::random(40);
        }

        $user->save();

        Auth::login($user, remember: true);

        return redirect()->intended(route('admin.dashboard'));
    }

    public function logout(): RedirectResponse
    {
        Auth::logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('login');
    }
}
