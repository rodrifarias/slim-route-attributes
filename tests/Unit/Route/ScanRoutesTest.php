<?php

namespace Rodrifarias\SlimRouteAttributes\Tests\Unit\Route;

use PHPUnit\Framework\TestCase;
use Rodrifarias\SlimRouteAttributes\Exception\DirectoryNotFoundException;
use Rodrifarias\SlimRouteAttributes\Exception\MiddlewareShouldImplementsMiddlewareInterfaceException;
use Rodrifarias\SlimRouteAttributes\Route\Route;
use Rodrifarias\SlimRouteAttributes\Route\Scan\ScanRoutes;

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
}
