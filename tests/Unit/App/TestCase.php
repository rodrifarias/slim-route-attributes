<?php

namespace Rodrifarias\SlimRouteAttributes\Tests\Unit\App;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Rodrifarias\SlimRouteAttributes\App\AppSlimFactory;
use Rodrifarias\SlimRouteAttributes\Route\Scan\ScanRoutes;
use Slim\App;
use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\Headers;
use Slim\Psr7\Request as SlimRequest;
use Slim\Psr7\Uri;

class TestCase extends PHPUnitTestCase
{
    public function get(string $path, array $headers = [], array $cookies = [], array $serverParams = []): ResponseInterface
    {
        return $this->request('GET', $path, $headers, $cookies, $serverParams);
    }

    public function post(string $path, array $headers = [], array $cookies = [], array $serverParams = []): ResponseInterface
    {
        return $this->request('POST', $path, $headers, $cookies, $serverParams);
    }

    public function put(string $path, array $headers = [], array $cookies = [], array $serverParams = []): ResponseInterface
    {
        return $this->request('PUT', $path, $headers, $cookies, $serverParams);
    }

    public function delete(string $path, array $headers = [], array $cookies = [], array $serverParams = []): ResponseInterface
    {
        return $this->request('DELETE', $path, $headers, $cookies, $serverParams);
    }

    public function patch(string $path, array $headers = [], array $cookies = [], array $serverParams = []): ResponseInterface
    {
        return $this->request('PATCH', $path, $headers, $cookies, $serverParams);
    }

    private function request(string $method, string $path, array $headers = [], array $cookies = [], array $serverParams = []): ResponseInterface
    {
        $app = $this->getAppInstance();
        $request = $this->createRequest($method, $path, $headers, $cookies, $serverParams);
        return $app->handle($request);
    }

    protected function getAppInstance(): App
    {
        $app = AppSlimFactory::create();
        $scan = new ScanRoutes();
        $app->registerRoutes(__DIR__ . '/../Route/RoutesControllers/Controller', $scan);
        return $app;
    }

    protected function createRequest(
        string $method,
        string $path,
        array $headers = ['HTTP_ACCEPT' => 'application/json'],
        array $cookies = [],
        array $serverParams = []
    ): Request {
        $uri = new Uri('', '', 80, $path);
        $handle = fopen('php://temp', 'w+');
        $stream = (new StreamFactory())->createStreamFromResource($handle);
        $h = new Headers();

        foreach ($headers as $name => $value) {
            $h->addHeader($name, $value);
        }

        return new SlimRequest($method, $uri, $h, $cookies, $serverParams, $stream);
    }
}
