<?php

namespace Rodrifarias\SlimRouteAttributes\Tests\Unit\App;

class HomeTest extends TestCase
{
    public function testPathGetHome(): void
    {
        $response = $this->get('/home');
        $this->assertEquals('Home', $response->getBody());
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testPathGetHome1(): void
    {
        $response = $this->get('/home/1');
        $this->assertEquals('Hello 1', $response->getBody());
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testPathPostHome(): void
    {
        $response = $this->post('/home');
        $this->assertEquals('MIDDLEWARE BEFORE - MIDDLEWARE AFTER', $response->getBody());
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testPathGetOptionalWithoutParamHome(): void
    {
        $response = $this->get('/home/optional');
        $this->assertEquals('Optional', $response->getBody());
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testPathGetOptionalWithParamHome(): void
    {
        $response = $this->get('/home/optional/123');
        $this->assertEquals('Optional123', $response->getBody());
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testPathPutHome(): void
    {
        $response = $this->put('/home/123');
        $this->assertEquals('Updated 123', $response->getBody());
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testPathDeleteHome(): void
    {
        $response = $this->delete('/home/123');
        $this->assertEquals(204, $response->getStatusCode());
    }

    public function testPathPatchHome(): void
    {
        $response = $this->patch('/home/123');
        $this->assertEquals('Updated Patch 123', $response->getBody());
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testPathMapGet(): void
    {
        $response = $this->get('/home/map/test');
        $this->assertEquals('Map Route with method [GET]', $response->getBody());
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testPathMapPost(): void
    {
        $response = $this->post('/home/map/test');
        $this->assertEquals('Map Route with method [POST]', $response->getBody());
        $this->assertEquals(200, $response->getStatusCode());
    }
}
