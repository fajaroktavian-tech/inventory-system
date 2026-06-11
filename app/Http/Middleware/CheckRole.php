<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!$request->user()) {
            return redirect('login');
        }

        // Jika role user ada di dalam daftar parameter yang diperbolehkan
        if (in_array($request->user()->role, $roles)) {
            return $next($request);
        }

        // Jika tidak punya akses, arahkan ke dashboard dengan pesan error
        return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
    }
}