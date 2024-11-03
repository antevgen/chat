<?php

declare(strict_types=1);

namespace Tests\Traits;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

trait HttpJsonTestTrait
{
    /**
     * @param null|array<array-key,mixed> $data
     */
    protected function createJsonRequest(
        string $method,
        string|UriInterface $uri,
        array $data = null
    ): ServerRequestInterface {
        $request = $this->createRequest($method, $uri);

        if ($data !== null) {
            $request->getBody()->write((string)json_encode($data));
        }

        return $request->withHeader('Content-Type', 'application/json');
    }

    /**
     * @param array<array-key,mixed> $expected
     */
    protected function assertJsonData(array $expected, ResponseInterface $response): void
    {
        $data = $this->getJsonData($response);

        $this->assertSame($expected, $data);
    }

    /**
     * @return array<array-key,mixed>
     */
    protected function getJsonData(ResponseInterface $response): array
    {
        $actual = (string)$response->getBody();
        $this->assertJson($actual);

        return (array) json_decode($actual, true);
    }

    protected function assertJsonContentType(ResponseInterface $response): void
    {
        $this->assertStringContainsString('application/json', $response->getHeaderLine('Content-Type'));
    }

    protected function assertJsonValue(mixed $expected, string $path, ResponseInterface $response): void
    {
        $this->assertSame($expected, $this->getArrayValue($this->getJsonData($response), $path));
    }
}
