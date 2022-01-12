<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Hr
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
        if(auth()->user()->role == 'admin') {
            return redirect(route('home'));
        } elseif(auth()->user()->role == 'user') {
            return redirect(route('employee.home'));
        } else {
            return $next($request);
        }
        return $next($request);
    }
}
