<?php

namespace Rodrifarias\SlimRouteAttributes\Route;

use Stringable;

class Route implements Stringable
{
    public function __construct(
        public readonly string $className,
        public readonly string $classMethod,
        public readonly string $httpMethod,
        public readonly string $path,
        public readonly bool $publicAccess,
        public readonly array $middleware,
    ) {
    }

    public function __toString(): string
    {
        return 'Class: ' . $this->className . PHP_EOL .
               'Method: ' . $this->classMethod . PHP_EOL .
               'Http Method: ' . strtoupper($this->httpMethod) . PHP_EOL .
               'Path : ' . $this->path . PHP_EOL .
               'Public Access : ' . ($this->publicAccess ? 'Yes' : 'No') . PHP_EOL .
               'Middlewares : ' . implode(', ', $this->middleware);
    }
}
