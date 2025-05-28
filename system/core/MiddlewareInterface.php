<?php

namespace System\core;

use System\Core\Request;
use System\Core\Response;
use Closure;

interface MiddlewareInterface
{
    /**
     * Handle the request and response.
     *
     * @param \System\Core\Request $request
     * @param \System\Core\Response $response
     * @param callable $next
     * @return mixed
     */
    public function handle(Request $request, Response $response, Closure $next);
}
