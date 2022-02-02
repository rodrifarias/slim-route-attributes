<?php

namespace Rodrifarias\SlimRouteAttributes\Attributes\HttpMethods;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class Delete extends AbstractHttpMethod
{
}
