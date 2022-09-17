<?php

namespace Rodrifarias\SlimRouteAttributes\Tests\Unit\Route\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;

class MiddlewareBefore implements MiddlewareInterface
{
    public function __construct(private string $message)
    {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);
        $existingContent = (string) $response->getBody();

        $response = new Response();
        $response->getBody()->write($this->message . ' - ' . $existingContent);

        return $response;
    }
}
