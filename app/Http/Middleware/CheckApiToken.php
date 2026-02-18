<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckApiToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Debug: Check if token exists in session
        \Log::info('Middleware check:', ['has_token' => session('api_token'), 'token_value' => session('api_token')]);
        
        if (!session('api_token')) {
            return redirect()->route('login')->with('error', 'Bu sayfaya erişmek için giriş yapmalısınız.');
        }

        return $next($request);
    }
}
