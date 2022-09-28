<?php

namespace Rodrifarias\SlimRouteAttributes\App;

use Psr\Cache\InvalidArgumentException;
use ReflectionException;
use Rodrifarias\SlimRouteAttributes\Exception\DirectoryNotFoundException;
use Rodrifarias\SlimRouteAttributes\Route\Scan\ScanRoutesInterface;
use Slim\App as SlimApp;

class App extends SlimApp
{
    /**
     * @throws ReflectionException
     * @throws DirectoryNotFoundException
     * @throws InvalidArgumentException
     */
    public function registerRoutes(string $path, ScanRoutesInterface $scanRoutes, bool $useCache = false): void
    {
        $routes = $scanRoutes->getRoutes($path, $useCache);

        foreach ($routes as $route) {
            $method = $route->httpMethod;
            $appRoute = $this->$method($route->path, [ $route->className, $route->classMethod ]);

            foreach ($route->middleware as $middleware) {
                $getClassContainer = is_string($middleware) && $this->container;
                $appRoute->add($getClassContainer ? $this->container->get($middleware) : $middleware);
            }
        }
    }
}
