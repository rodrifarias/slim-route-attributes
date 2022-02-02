# Slim Route Attributes
Slim Route Attributes is a route scanner that uses PHP attributes.

## Installation
```bash
$ composer require rodrifarias/slim-route-attributes
```

## Hello World using AppFactory
Create file public/index.php.

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use Rodrifarias\SlimRouteAttributes\App\AppSlimFactory;

$pathDirControllers = __DIR__ . '/your-dir';

$app = AppSlimFactory::create();
$app->registerRoutes($pathDirControllers);
$app->run();
```

## Creating a controller
```php
<?php

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Rodrifarias\SlimRouteAttributes\Attributes\HttpMethods\Delete;
use Rodrifarias\SlimRouteAttributes\Attributes\HttpMethods\Get;
use Rodrifarias\SlimRouteAttributes\Attributes\HttpMethods\Patch;
use Rodrifarias\SlimRouteAttributes\Attributes\HttpMethods\Post;
use Rodrifarias\SlimRouteAttributes\Attributes\HttpMethods\Put;
use Rodrifarias\SlimRouteAttributes\Attributes\Middleware;
use Rodrifarias\SlimRouteAttributes\Attributes\PublicAccess;
use Rodrifarias\SlimRouteAttributes\Attributes\Route;
use Rodrifarias\SlimRouteAttributes\Tests\Unit\Route\Middleware\MiddlewareAfter;
use Rodrifarias\SlimRouteAttributes\Tests\Unit\Route\Middleware\MiddlewareBefore;

#[Route('/')]
class HomeController
{
    #[Get, PublicAccess(true)]
    public function showAll(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $response->getBody()->write('Home');
        return $response->withHeader('Content-type', 'application/json');
    }

    #[Get('/optional[/{id:[0-9]+}]')]
    public function optional(RequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = $args['id'] ?? '';
        $response->getBody()->write('Optional' . $id);
        return $response->withHeader('Content-type', 'application/json');
    }

    #[Get('/{id:\d+}'), PublicAccess(true)]
    public function show(RequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $response->getBody()->write('Hello ' . $args['id']);
        $response->withHeader('Content-type', 'application/json');
        return $response->withStatus(200);
    }

    #[Post, Middleware([MiddlewareAfter::class, MiddlewareBefore::class])]
    public function create(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $response->withHeader('Content-type', 'application/json');
    }

    #[Put('/{id:\d+}')]
    public function update(RequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $response->getBody()->write('Updated ' . $args['id']);
        return $response->withHeader('Content-type', 'application/json');
    }

    #[Delete('/{id:\d+}')]
    public function delete(RequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $response->withHeader('Content-type', 'application/json');
        return $response->withStatus(204);
    }

    #[Patch('/{id:\d+}')]
    public function updatePatch(RequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $response->getBody()->write('Updated Patch ' . $args['id']);
        return $response->withHeader('Content-type', 'application/json');
    }
}
```

You may quickly test this using the built-in PHP server:
```bash
$ php -S localhost:8000 -t public
```
Going to http://localhost:8000 will now display "Home".

## Available Http Methods
GET, POST, PUT, DELETE, PATCH

## Middleware in Route
To run a middleware you have to add the following attribute (Middleware) in the method
```php
#[Route('/home')]
class HomeController
{
    #[Get, PublicAccess(true), Middleware([MiddlewareAfter::class])]
    public function showAll(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $response->getBody()->write('Home');
        return $response->withHeader('Content-type', 'application/json');
    }
}
```
## Command to show all registered routes
```bash
$ php vendor/bin/show-routes.php show-routes --path=/your-dir
```

| Route | Http Method | Controller Method | IsPublic |
|-------|-------------|-------------------|----------|
| /     | GET         | Controller:method | yes      |

## Get list routes
```php
<?php

use Rodrifarias\SlimRouteAttributes\ScanRoutes;

require_once __DIR__ . '/vendor/autoload.php';

$scan = new ScanRoutes();
$routes = $scan->getRoutes(__DIR__ . '/tests');

foreach ($routes as $route) {
    echo $route . PHP_EOL . PHP_EOL;
}
```

## Tests
To execute the test suite, you'll need to install all development dependencies.

```bash
$ git clone https://github.com/rodrifarias/slim-route-attributes
$ composer install
$ composer test
```
