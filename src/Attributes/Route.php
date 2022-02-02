<?php

namespace Rodrifarias\SlimRouteAttributes\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Route
{
    public function __construct(string $prefixRoute)
    {
    }
}
