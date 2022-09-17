<?php

namespace Rodrifarias\SlimRouteAttributes\App;

use Psr\Cache\InvalidArgumentException;
use ReflectionException;
use Rodrifarias\SlimRouteAttributes\Exception\DirectoryNotFoundException;
use Rodrifarias\SlimRouteAttributes\Route\Route;
use Rodrifarias\SlimRouteAttributes\Route\Scan\ScanRoutesInterface;
use Slim\App as SlimApp;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;

class App extends SlimApp
{
    /**
     * @throws ReflectionException
     * @throws DirectoryNotFoundException
     * @throws InvalidArgumentException
     */
    public function registerRoutes(string $path, ScanRoutesInterface $scanRoutes, bool $useCache = false): void
    {
        $routes = $this->getRoutes($path, $scanRoutes, $useCache);

        foreach ($routes as $route) {
            $method = $route->httpMethod;
            $appRoute = $this->$method($route->path, [ $route->className, $route->classMethod ]);

            foreach ($route->middleware as $middleware) {
                $getClassContainer = is_string($middleware) && $this->container;
                $appRoute->add($getClassContainer ? $this->container->get($middleware) : $middleware);
            }
        }
    }

    /**
     * @param string $path
     * @param ScanRoutesInterface $scanRoutes
     * @param bool $cache
     * @return Route[]
     * @throws InvalidArgumentException
     */
    private function getRoutes(string $path, ScanRoutesInterface $scanRoutes, bool $cache = false): array
    {
        $cacheSystem = new FilesystemAdapter();

        if (!$cache) {
            return $scanRoutes->getRoutes($path);
        }

        return $cacheSystem->get('app-routes', function (ItemInterface $item) use ($path, $scanRoutes) {
            $item->expiresAfter(10000);
            return $scanRoutes->getRoutes($path);
        });
    }
}
