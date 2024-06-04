<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceJsonValidation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('POST') && empty($request->all())) {
            // Trả về lỗi 422 nếu không có dữ liệu
            return response()->json(['errors' => ['message' => 'aaaaaaaaa']], 401);
        }

        // Tiếp tục xử lý request
        return $next($request);

    }
}
