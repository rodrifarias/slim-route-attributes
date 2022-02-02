<?php

namespace Rodrifarias\SlimRouteAttributes\Tests\Unit\Route;

use PHPUnit\Framework\TestCase;
use Rodrifarias\SlimRouteAttributes\RouteParsed;

class RouteParsedTest extends TestCase
{
    public function testReturn1PossibleRoute(): void
    {
        $route = '/test';
        $routeParsed = RouteParsed::getRoutesFromRoute($route);
        $this->assertCount(1, $routeParsed);
        $this->assertEquals($route, $routeParsed[0]);
    }

    public function testReturn1PossibleRouteWith2Address(): void
    {
        $route = '/test/abc';
        $routeParsed = RouteParsed::getRoutesFromRoute($route);
        $this->assertCount(1, $routeParsed);
        $this->assertEquals($route, $routeParsed[0]);
    }

    public function testReturn1PossibleRouteWithVariablePath(): void
    {
        $route = '/test/{abc:\d+}';
        $routeParsed = RouteParsed::getRoutesFromRoute($route);
        $this->assertCount(1, $routeParsed);
        $this->assertEquals('/test/\d+', $routeParsed[0]);
    }

    public function testReturn2PossibleRouteWhenRouteHavePathVariableOptional(): void
    {
        $route = '/test[/{id}]';
        $routeParsed = RouteParsed::getRoutesFromRoute($route);
        $this->assertCount(2, $routeParsed);
        $this->assertEquals('/test/[^/]+', $routeParsed[0]);
        $this->assertEquals('/test', $routeParsed[1]);
    }

    public function testReturn3PossibleRouteWhenRouteHave2PathVariableOptional(): void
    {
        $route = '/test[/{id}[/{name:\D+}]]';
        $routeParsed = RouteParsed::getRoutesFromRoute($route);
        $this->assertCount(3, $routeParsed);
        $this->assertEquals('/test/[^/]+/\D+', $routeParsed[0]);
        $this->assertEquals('/test/[^/]+', $routeParsed[1]);
        $this->assertEquals('/test', $routeParsed[2]);
    }

    public function testReturn4PossibleRouteWhenRouteHave3PathVariableOptionalWithRegexPattern(): void
    {
        $route = '/test[/{id:[0-9]+}[/{name:[a-z]+}[/{age:[1-9]+}]]]';
        $routeParsed = RouteParsed::getRoutesFromRoute($route);

        $this->assertCount(4, $routeParsed);
        $this->assertEquals('/test/[0-9]+/[a-z]+/[1-9]+', $routeParsed[0]);
        $this->assertEquals('/test/[0-9]+/[a-z]+', $routeParsed[1]);
        $this->assertEquals('/test/[0-9]+', $routeParsed[2]);
        $this->assertEquals('/test', $routeParsed[3]);
    }
}
