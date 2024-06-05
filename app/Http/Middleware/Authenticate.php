<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use App\Traits\APIResponse;


class Authenticate extends Middleware
{
    use APIResponse;

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    public function handle($request, $next, ...$guards)
    {
        $this->authenticate($request, $guards);

        if ($request->expectsJson()) {
            return $next($request);
        }
        return $this->responseUnAuthenticated();
    }


}
