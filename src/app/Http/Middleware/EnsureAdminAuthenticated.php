<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminAuthenticated
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $email = Str::lower((string) Auth::user()?->email);

        if (! Auth::check() || ! in_array($email, config('admin.emails', []), true)) {
            return redirect()
                ->route('login')
                ->with('status', 'Silakan login dengan Google memakai email admin terlebih dahulu.');
        }

        return $next($request);
    }
}
