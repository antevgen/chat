<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

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
    public function list(Request $request, Response $response): ResponseInterface
    {
        // Code to retrieve and return a list of groups
    }
}
