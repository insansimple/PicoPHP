<?php

namespace App\Middleware;

use System\Core\MiddlewareInterface;
use System\Core\Request;
use System\Core\Response;
use Closure;

class AuthMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, Response $response,  Closure $next)
    {
        return $next($request);
    }
}
