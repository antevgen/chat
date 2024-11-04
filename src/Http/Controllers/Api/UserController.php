<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Response\JsonResponse;
use App\Services\UserService;
use Fig\Http\Message\StatusCodeInterface;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class UserController
{
    public function __construct(
        private readonly JsonResponse $response,
        protected UserService $userService,
    ) {
    }

    #[OA\Get(
        path: '/users',
        summary: 'Get a paginated list of users',
        tags: ['Users'],
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
                description: 'List of users',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        ref: "#/components/schemas/User"
                    )
                )
            ),
        ]
    )]
    public function list(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $page = (int) ($request->getQueryParams()['page'] ?? 1);
        $limit = (int) ($request->getQueryParams()['limit'] ?? 10);
        $groups = $this->userService->getPaginatedList($page, $limit);

        return $this->response->json($response, $groups);
    }

    #[OA\Post(
        path: '/users',
        summary: 'Create a new user',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['username', 'email'],
                properties: [
                    new OA\Property(property: 'username', type: 'string', example: 'johndoe'),
                    new OA\Property(property: 'email', type: 'string', example: 'johndoe@example.com')
                ]
            )
        ),
        tags: ['Users'],
        responses: [
            new OA\Response(
                response: 201,
                description: 'User created successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 1),
                        new OA\Property(property: 'username', type: 'string', example: 'johndoe'),
                        new OA\Property(property: 'email', type: 'string', example: 'johndoe@example.com')
                    ]
                )
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
        $group = $this->userService->createUser($data['username'], $data['email']);

        return $this->response->json($response, $group)
            ->withStatus(StatusCodeInterface::STATUS_CREATED);
    }
}
