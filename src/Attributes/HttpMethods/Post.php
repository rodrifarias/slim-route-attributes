<?php

namespace Rodrifarias\SlimRouteAttributes\Attributes\HttpMethods;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class Post extends AbstractHttpMethod
{
}
