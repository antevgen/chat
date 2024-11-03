<?php

declare(strict_types=1);

namespace App\Response;

use Psr\Http\Message\ResponseInterface;

class JsonResponse
{
    public function json(
        ResponseInterface $response,
        mixed $data = null,
    ): ResponseInterface {
        $response = $response->withHeader('Content-Type', 'application/json');

        if (is_object($data) && method_exists($data, 'toArray')) {
            $data = $data->toArray();
        }

        $response->getBody()->write(
            (string)json_encode(
                $data,
                JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR
            )
        );

        return $response;
    }
}
