<?php

declare(strict_types=1);

namespace IA\Cookie;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CookieJarMiddleware implements MiddlewareInterface
{
    /**
     * @param CookieJarInterface $cookieJar
     */
    public function __construct(protected CookieJarInterface $cookieJar)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        foreach ($this->cookieJar->all() as $cookie) {
            $response = $response->withHeader('Set-Cookie', (string)$cookie);
        }

        return $response;
    }
}