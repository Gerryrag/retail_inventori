<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminAuthenticated
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! (bool) $request->session()->get('admin_authenticated', false)) {
            return redirect()
                ->route('login')
                ->with('status', 'Silakan login sebagai admin terlebih dahulu.');
        }

        return $next($request);
    }
}
