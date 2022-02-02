<?php

namespace Rodrifarias\SlimRouteAttributes\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class PublicAccess
{
    public function __construct(bool $value = false)
    {
    }
}
