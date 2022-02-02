<?php

namespace Rodrifarias\SlimRouteAttributes;

use FastRoute\RouteParser\Std;

class RouteParsed
{
    /**
     * @param Route[] $routesInfo
     */
    public static function getRoutesFromListRoutes(array $routesInfo): array
    {
        return array_map(function ($route) {
            $possibleRoutes = self::getRoutesFromRoute($route->path);
            return array_map(fn ($p) => ['method' => $route->httpMethod, 'path' => $p], $possibleRoutes);
        }, $routesInfo);
    }

    /**
     * @return string[]
     */
    public static function getRoutesFromRoute(string $patternRoute): array
    {
        $parseUrlStd = new Std();
        $urlParsed = $parseUrlStd->parse($patternRoute);
        $possibleRoutes = array_map(fn ($url) => self::getPossibleRoutes($url), $urlParsed);

        return array_reverse($possibleRoutes);
    }

    private static function getPossibleRoutes(array $route): string
    {
        $routeStr = $route[0];

        foreach ($route as $key => $item) {
            if ($key > 0) {
                $routeStr .= is_array($item) ? $item[1] : $item;
            }
        }

        return $routeStr;
    }
}
