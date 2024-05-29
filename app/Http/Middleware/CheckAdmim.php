<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Traits\APIResponse;


class CheckAdmim
{
    use APIResponse;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->role === 0) {
            return $next($request);
        }

        return $this->responseUnAuthorized('Bạn không có quyền truy cập', Response::HTTP_FORBIDDEN);
    }
}
