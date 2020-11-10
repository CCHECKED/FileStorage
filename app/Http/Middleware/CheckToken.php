<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if($request->query('token') != '4f50fe592ed735defa4df58d477bcbca7d079396af80f58627fa37f1182f9412') {
            return response('Доступ запрещен!', 403);
        }
        return $next($request);
    }
}
