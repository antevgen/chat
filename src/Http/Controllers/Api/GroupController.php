<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Response\JsonResponse;
use App\Services\GroupService;
use Fig\Http\Message\StatusCodeInterface;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class GroupController
{
    public function __construct(
        private readonly JsonResponse $response,
        protected GroupService $groupService
    ) {
    }

    #[OA\Get(
        path: "/groups",
        summary: "Get a paginated list of groups",
        tags: ["Groups"],
        parameters: [
            new OA\Parameter(
                name: 'page',
                description: 'Page number for pagination',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer', default: 1)
            ),
            new OA\Parameter(
                name: 'limit',
                description: 'Number of items per page',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer', default: 10)
            ),
        ],
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
        $page = (int) ($request->getQueryParams()['page'] ?? 1);
        $limit = (int) ($request->getQueryParams()['limit'] ?? 10);
        $groups = $this->groupService->getPaginatedGroups($page, $limit);

        return $this->response->json($response, $groups);
    }


    #[OA\Post(
        path: "/groups",
        summary: "Create a new chat group",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name"],
                properties: [
                    new OA\Property(
                        property: "name",
                        description: "The name of the chat group",
                        type: "string",
                        example: "Developers Group"
                    )
                ]
            )
        ),
        tags: ["Groups"],
        responses: [
            new OA\Response(
                response: 201,
                description: "Chat group created successfully",
                content: new OA\JsonContent(ref: "#/components/schemas/Group")
            ),
            new OA\Response(
                response: 422,
                description: "Validation error",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "errors",
                            description: "Validation error details",
                            type: "object",
                            additionalProperties: new OA\AdditionalProperties(
                                properties: [
                                    new OA\Property(
                                        type: "array",
                                        items: new OA\Items(type: "string")
                                    )
                                ]
                            ),
                        )
                    ]
                )
            )
        ]
    )]
    public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        /** @var array<string, mixed> $data */
        $data = $request->getParsedBody();
        $group = $this->groupService->createGroup($data['name']);

        return $this->response->json($response, $group)
            ->withStatus(StatusCodeInterface::STATUS_CREATED);
    }
}
