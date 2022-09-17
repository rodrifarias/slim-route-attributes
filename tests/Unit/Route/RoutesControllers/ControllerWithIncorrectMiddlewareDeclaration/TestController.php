<?php

namespace Rodrifarias\SlimRouteAttributes\Tests\Unit\Route\RoutesControllers\ControllerWithIncorrectMiddlewareDeclaration;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Rodrifarias\SlimRouteAttributes\Attributes\HttpMethods\Post;
use Rodrifarias\SlimRouteAttributes\Attributes\Middleware;
use Rodrifarias\SlimRouteAttributes\Attributes\Route;
use stdClass;

#[Route('/test')]
class TestController
{
    #[Post, Middleware([1, new stdClass()])]
    public function create(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $response->withHeader('Content-type', 'application/json');
    }
}
