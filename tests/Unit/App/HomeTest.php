<?php

namespace Rodrifarias\SlimRouteAttributes\Tests\Unit\App;

class HomeTest extends TestCase
{
    public function testPathGetHome(): void
    {
        $app = $this->getAppInstance();
        $request = $this->createRequest('GET', '/home');
        $response = $app->handle($request);
        $this->assertEquals('Home', $response->getBody());
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testPathGetHome1(): void
    {
        $app = $this->getAppInstance();
        $request = $this->createRequest('GET', '/home/1');
        $response = $app->handle($request);
        $this->assertEquals('Hello 1', $response->getBody());
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testPathPostHome(): void
    {
        $app = $this->getAppInstance();
        $request = $this->createRequest('POST', '/home');
        $response = $app->handle($request);
        $this->assertEquals('MIDDLEWARE BEFORE - MIDDLEWARE AFTER', $response->getBody());
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testPathGetOptionalWithoutParamHome(): void
    {
        $app = $this->getAppInstance();
        $request = $this->createRequest('GET', '/home/optional');
        $response = $app->handle($request);
        $this->assertEquals('Optional', $response->getBody());
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testPathGetOptionalWithParamHome(): void
    {
        $app = $this->getAppInstance();
        $request = $this->createRequest('GET', '/home/optional/123');
        $response = $app->handle($request);
        $this->assertEquals('Optional123', $response->getBody());
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testPathPutHome(): void
    {
        $app = $this->getAppInstance();
        $request = $this->createRequest('PUT', '/home/123');
        $response = $app->handle($request);
        $this->assertEquals('Updated 123', $response->getBody());
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testPathDeleteHome(): void
    {
        $app = $this->getAppInstance();
        $request = $this->createRequest('DELETE', '/home/123');
        $response = $app->handle($request);
        $this->assertEquals(204, $response->getStatusCode());
    }

    public function testPathPatchHome(): void
    {
        $app = $this->getAppInstance();
        $request = $this->createRequest('PATCH', '/home/123');
        $response = $app->handle($request);
        $this->assertEquals('Updated Patch 123', $response->getBody());
        $this->assertEquals(200, $response->getStatusCode());
    }
}
