<?php

namespace Rodrifarias\SlimRouteAttributes\Attributes\HttpMethods;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class Map extends AbstractHttpMethod
{
    public function __construct(string $path = '', array $methods = [])
    {
        parent::__construct($path);
    }
}
