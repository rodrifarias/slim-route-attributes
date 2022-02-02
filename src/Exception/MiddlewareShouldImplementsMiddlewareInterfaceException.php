<?php

namespace Rodrifarias\SlimRouteAttributes\Exception;

use Exception;

class MiddlewareShouldImplementsMiddlewareInterfaceException extends Exception
{
    public function __construct()
    {
        parent::__construct('Middleware should implements MiddlewareInterface', 500);
    }
}
