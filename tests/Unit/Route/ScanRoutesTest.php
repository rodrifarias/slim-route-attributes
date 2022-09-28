<?php

namespace Rodrifarias\SlimRouteAttributes\Tests\Unit\Route;

use PHPUnit\Framework\TestCase;
use Rodrifarias\SlimRouteAttributes\Exception\DirectoryNotFoundException;
use Rodrifarias\SlimRouteAttributes\Exception\MiddlewareShouldImplementsMiddlewareInterfaceException;
use Rodrifarias\SlimRouteAttributes\Route\Route;
use Rodrifarias\SlimRouteAttributes\Route\Scan\ScanRoutes;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;

class ScanRoutesTest extends TestCase
{
    private string $dirBase = __DIR__ . '/./RoutesControllers/';

    /**
     * @dataProvider valueProviderRoutes
     */
    public function testRoutes(int $count, array $routes): void
    {
        $this->assertCount($count, $routes);
    }

    public function valueProviderRoutes(): array
    {
        $scanRoutes = new ScanRoutes();
        $routes = $scanRoutes->getRoutes($this->dirBase . 'Controller');
        $emptyDirRoutes = $scanRoutes->getRoutes($this->dirBase . 'Empty');
        $routesClassWithoutAttributes = $scanRoutes->getRoutes($this->dirBase . 'Test');
        $filterRoutesMiddleware = array_filter($routes, fn (Route $r) => count($r->middleware) > 0);
        $filterRoutesMethodGet = array_filter($routes, fn (Route $r) => $r->httpMethod === 'get');
        $filterRoutesMethodPost = array_filter($routes, fn (Route $r) => $r->httpMethod === 'post');
        $filterRoutesMethodPut = array_filter($routes, fn (Route $r) => $r->httpMethod === 'put');
        $filterRoutesMethodDelete = array_filter($routes, fn (Route $r) => $r->httpMethod === 'delete');
        $filterRoutesMethodPatch = array_filter($routes, fn (Route $r) => $r->httpMethod === 'patch');
        $filterRoutesPublicAccessTrue = array_filter($routes, fn (Route $r) => $r->publicAccess);
        $filterRoutesPublicAccessFalse = array_filter($routes, fn (Route $r) => !$r->publicAccess);
        $routesIncomplete = $scanRoutes->getRoutes($this->dirBase . 'IncompleteController');

        return [
            'ShouldHave9RoutesMapped' => [9, $routes],
            'ShouldNotRoutesWhenDirectoryIsEmpty' => [0, $emptyDirRoutes],
            'ShouldNotRoutesWhenDirectoryOnlyHaveClassWithoutAttributes' => [0, $routesClassWithoutAttributes],
            'ShouldHave1RouteWithMiddlewares' => [1, $filterRoutesMiddleware],
            'ShouldHave3RouteMethodsGet' => [4, $filterRoutesMethodGet],
            'ShouldHave2RouteMethodsPost' => [2, $filterRoutesMethodPost],
            'ShouldHave2RouteMethodsPut' => [1, $filterRoutesMethodPut],
            'ShouldHave2RouteMethodsDelete' => [1, $filterRoutesMethodDelete],
            'ShouldHave2RouteMethodsPatch' => [1, $filterRoutesMethodPatch],
            'ShouldHave2RoutesWithPublicAccessTrue' => [2, $filterRoutesPublicAccessTrue],
            'ShouldHave2RoutesWithPublicAccessFalse' => [7, $filterRoutesPublicAccessFalse],
            'ShouldNotScanClassWithIncompleteAttributes' => [0, $routesIncomplete],
        ];
    }

    public function testShouldGenerateDirectoryNotFoundExceptionWhenPathNotExists(): void
    {
        $this->expectException(DirectoryNotFoundException::class);
        $scanRoutes = new ScanRoutes();
        $scanRoutes->getRoutes('/dir-not-exists');
    }

    public function testShouldGenerateExceptionWhenScanRouteAndRouteHasAIncorrectDeclarationMiddleware(): void
    {
        $this->expectException(MiddlewareShouldImplementsMiddlewareInterfaceException::class);
        $this->expectExceptionMessage('Middleware should implements MiddlewareInterface');
        $this->expectExceptionCode(500);

        $scanRoutes = new ScanRoutes();
        $scanRoutes->getRoutes($this->dirBase . 'ControllerWithIncorrectMiddlewareDeclaration');
    }

    public function testShouldGetRoutesFromCache(): void
    {
        $cacheSystem = new FilesystemAdapter();
        $cacheSystem->delete('scan-app-routes');
        $cacheSystem->get('scan-app-routes', fn (ItemInterface $item) => [
            new Route('CLASS_NAME', 'CLASS_METHOD', 'HTTP_METHOD', '/api', true, []),
            new Route('CLASS_NAME_2', 'CLASS_METHOD_2', 'HTTP_METHOD_2', '/api-2', false, []),
        ]);

        $scanRoutes = new ScanRoutes();
        $routes = $scanRoutes->getRoutes($this->dirBase . 'Controller', true);

        $this->assertCount(2, $routes);
        $this->assertSame('CLASS_NAME', $routes[0]->className);
        $this->assertSame('CLASS_NAME_2', $routes[1]->className);
        $this->assertSame('CLASS_METHOD', $routes[0]->classMethod);
        $this->assertSame('CLASS_METHOD_2', $routes[1]->classMethod);
        $this->assertSame('HTTP_METHOD', $routes[0]->httpMethod);
        $this->assertSame('HTTP_METHOD_2', $routes[1]->httpMethod);
        $this->assertSame('/api', $routes[0]->path);
        $this->assertSame('/api-2', $routes[1]->path);
        $this->assertTrue($routes[0]->publicAccess);
        $this->assertFalse($routes[1]->publicAccess);
    }
}
