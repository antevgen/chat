<?php

declare(strict_types=1);

namespace Tests\Traits;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use RuntimeException;

trait HttpTestTrait
{
    /**
     * @param array<array-key,mixed> $serverParams
     *
     * @throws RuntimeException
     */
    protected function createRequest(
        string $method,
        string|UriInterface $uri,
        array $serverParams = []
    ): ServerRequestInterface {
        if (!$this->container instanceof ContainerInterface) {
            throw new RuntimeException('DI container not found');
        }

        $factory = $this->container->get(ServerRequestFactoryInterface::class);

        return $factory->createServerRequest($method, $uri, $serverParams);
    }

    /**
     * @param null|array<array-key,mixed> $data
     */
    protected function createFormRequest(
        string $method,
        string|UriInterface $uri,
        array $data = null
    ): ServerRequestInterface {
        $request = $this->createRequest($method, $uri);

        if ($data !== null) {
            $request = $request->withParsedBody($data);
        }

        return $request->withHeader('Content-Type', 'application/x-www-form-urlencoded');
    }

    protected function createResponse(int $code = 200, string $reasonPhrase = ''): ResponseInterface
    {
        if (!$this->container instanceof ContainerInterface) {
            throw new RuntimeException('DI container not found');
        }

        $factory = $this->container->get(ResponseFactoryInterface::class);

        return $factory->createResponse($code, $reasonPhrase);
    }

    protected function assertResponseContains(string $expected, ResponseInterface $response): void
    {
        $body = (string)$response->getBody();

        $this->assertStringContainsString($expected, $body);
    }
}
