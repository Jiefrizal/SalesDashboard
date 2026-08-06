<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MenuAccessMiddleware
{
    /**
     * Handle an incoming request.
     * Usage in routes: ->middleware('menu:stu_unit')
     */
    public function handle(Request $request, Closure $next, string $menuKey): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (!auth()->user()->hasMenuAccess($menuKey)) {
            return redirect('/')->with('error', 'Akses ditolak. Anda tidak memiliki hak akses untuk menu ini.');
        }

        return $next($request);
    }
}
