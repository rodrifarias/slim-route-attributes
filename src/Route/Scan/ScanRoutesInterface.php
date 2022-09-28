<?php

namespace Rodrifarias\SlimRouteAttributes\Route\Scan;

use Rodrifarias\SlimRouteAttributes\Route\Route;

interface ScanRoutesInterface
{
    /**
     * @return Route[]
     */
    public function getRoutes(string $path, bool $fromCache): array;
}
