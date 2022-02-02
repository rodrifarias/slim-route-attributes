<?php

namespace Rodrifarias\SlimRouteAttributes\App;

use Psr\Cache\InvalidArgumentException;
use ReflectionException;
use Rodrifarias\SlimRouteAttributes\Exception\DirectoryNotFoundException;
use Rodrifarias\SlimRouteAttributes\Route;
use Rodrifarias\SlimRouteAttributes\ScanRoutes;
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
    public function registerRoutes(string $path, bool $useCache = false): void
    {
        $routes = $this->getRoutes($path, $useCache);

        foreach ($routes as $route) {
            $method = $route->httpMethod;
            $appRoute = $this->$method($route->path, [ $route->className, $route->classMethod ]);

            foreach ($route->middleware as $middleware) {
                $appRoute->add(new $middleware());
            }
        }
    }

    /**
     * @param string $path
     * @param bool $cache
     * @throws InvalidArgumentException
     * @throws ReflectionException
     * @throws DirectoryNotFoundException
     * @return Route[]
     */
    private function getRoutes(string $path, bool $cache = false): array
    {
        $cacheSystem = new FilesystemAdapter();
        $scan = new ScanRoutes();

        if (!$cache) {
            return $scan->getRoutes($path);
        }

        return $cacheSystem->get('app-routes', function (ItemInterface $item) use ($path, $scan) {
            $item->expiresAfter(10000);
            return $scan->getRoutes($path);
        });
    }
}
