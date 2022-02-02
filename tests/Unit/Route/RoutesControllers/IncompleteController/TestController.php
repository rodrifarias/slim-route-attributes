<?php

namespace Rodrifarias\SlimRouteAttributes\Tests\Unit\Route\RoutesControllers\IncompleteController;

use Rodrifarias\SlimRouteAttributes\Attributes\Route;

#[Route('/test')]
class TestController
{
    public function showAll(): void
    {
    }
}
