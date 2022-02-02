<?php

namespace Rodrifarias\SlimRouteAttributes\Attributes;

use Attribute;
use Psr\Http\Server\MiddlewareInterface;
use ReflectionClass;
use ReflectionException;
use Rodrifarias\SlimRouteAttributes\Exception\MiddlewareShouldImplementsMiddlewareInterfaceException;

#[Attribute(Attribute::TARGET_METHOD)]
class Middleware
{
    /**
     * @param string[] $middlewares namespaceClass
     * @throws ReflectionException
     * @throws MiddlewareShouldImplementsMiddlewareInterfaceException
     */
    public function __construct(array $middlewares)
    {
        $middlewaresImplementsMiddlewareInterface = array_filter($middlewares, function ($m) {
            if (!is_string($m)) {
                return false;
            }

            $reflectionClass = new ReflectionClass($m);
            return $reflectionClass->implementsInterface(MiddlewareInterface::class);
        });

        if (count($middlewaresImplementsMiddlewareInterface) !== count($middlewares)) {
            throw new MiddlewareShouldImplementsMiddlewareInterfaceException();
        }
    }
}
