<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RestrictIP
{
    protected $allowedIps = [ 
        // '127.0.0.1',    
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();

        if (!in_array($ip, $this->allowedIps)) {
            abort(403, "Access denied — your IP ($ip) is not allowed.");
        }


        return $next($request);
    }
}
