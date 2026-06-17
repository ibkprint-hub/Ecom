<?php

namespace App\Http\Middleware;

use App\Support\Installer;
use Closure;
use Illuminate\Http\Request;

class EnsureInstalled
{
    public function handle(Request $request, Closure $next)
    {
        if (app()->runningUnitTests()) {
            return $next($request);
        }

        if (! Installer::isInstalled() && ! $request->is('install', 'install/*')) {
            return redirect()->route('install.index');
        }

        return $next($request);
    }
}
