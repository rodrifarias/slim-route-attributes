<?php

namespace Rodrifarias\SlimRouteAttributes\Attributes;

use Attribute;
use Psr\Http\Server\MiddlewareInterface;

#[Attribute(Attribute::TARGET_METHOD)]
class Middleware
{
    /**
     * @param string[]|MiddlewareInterface[] $middlewares namespaceClass or instance
     */
    public function __construct(array $middlewares)
    {
    }
}
