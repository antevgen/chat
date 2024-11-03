<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class GroupController
{
    #[OA\Get(
        path: "/groups",
        summary: "List all groups",
        tags: ["Groups"],
        responses: [
            new OA\Response(
                response: 200,
                description: "List of all chat groups",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(ref: "#/components/schemas/Group")
                )
            )
        ]
    )]
    public function list(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        // Code to retrieve and return a list of groups
    }
}
