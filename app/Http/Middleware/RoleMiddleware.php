<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if($request->routeIs('karyawan.*') && Auth::user()->is_admin !== 1){
            abort(403, 'Unauthorized action.');
        } elseif ($request->routeIs('ruangan.*') && Auth::user()->is_admin !== 1) {
            abort(403, 'Unauthorized action.');
        }
        
        return $next($request);
    }
}
