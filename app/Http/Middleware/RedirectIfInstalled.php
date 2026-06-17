<?php

namespace App\Http\Middleware;

use App\Support\Installer;
use Closure;
use Illuminate\Http\Request;

class RedirectIfInstalled
{
    public function handle(Request $request, Closure $next)
    {
        if (Installer::isInstalled()) {
            return redirect()->route('home');
        }

        return $next($request);
    }
}
