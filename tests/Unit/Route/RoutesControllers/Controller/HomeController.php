<?php

namespace Rodrifarias\SlimRouteAttributes\Tests\Unit\Route\RoutesControllers\Controller;

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

#[Route('/home')]
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
        return $response->withHeader('Content-type', 'application/json');
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
