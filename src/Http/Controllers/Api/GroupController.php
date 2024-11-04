<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Entity\User;
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

    #[OA\Post(
        path: "/groups/{id}/members",
        summary: "Join a specific chat group",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["user_id"],
                properties: [
                    new OA\Property(
                        property: "user_id",
                        description: "ID of the user joining the group",
                        type: "integer",
                        example: 123
                    )
                ]
            )
        ),
        tags: ["Groups"],
        parameters: [
            new OA\Parameter(
                name: "id",
                description: "ID of the group to join",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "User successfully joined the group",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "success",
                            type: "boolean",
                            example: true
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Group not found",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "message",
                            type: "string",
                            example: "Group \"1\" doesn't exist."
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 409,
                description: "User is already a member of the group",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "message",
                            type: "string",
                            example: "User 123 is already a member of group 1."
                        )
                    ]
                )
            )
        ]
    )]
    public function join(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        /** @var array<string, mixed> $data */
        $data = $request->getParsedBody();
        $groupId = (int) ($request->getAttributes()['id'] ?? 1);
        $group = $this->groupService->getGroup($groupId);
        if (!$group) {
            return $this->response->json($response, ['message' => sprintf('Group "%d" doesn\'t exist.', $groupId)])
                ->withStatus(StatusCodeInterface::STATUS_NOT_FOUND);
        }

        if ($this->groupService->isExistingMember($group, $data['user_id'])) {
            return $this->response->json(
                $response,
                ['message' => sprintf('User %d is already a member of group %d.', $data['user_id'], $groupId)]
            )->withStatus(StatusCodeInterface::STATUS_CONFLICT);
        }

        $this->groupService->addMember($group, $data['user_id']);
        return $this->response->json($response, ['success' => true])
            ->withStatus(StatusCodeInterface::STATUS_OK);
    }
}
