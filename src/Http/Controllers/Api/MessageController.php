<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Repository\MessageRepository;
use App\Response\JsonResponse;
use App\Services\GroupService;
use App\Services\MessageService;
use App\Services\UserService;
use Fig\Http\Message\StatusCodeInterface;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class MessageController
{
    public function __construct(
        private readonly JsonResponse $response,
        protected MessageService $messageService,
        private readonly GroupService $groupService,
        private readonly UserService $userService,
    ) {
    }

    #[OA\Post(
        path: "/groups/{id}/messages",
        summary: "Send a message to a group",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["subject", "content", "user_id"],
                properties: [
                    new OA\Property(
                        property: "subject",
                        description: "Subject of the message",
                        type: "string",
                        example: "Greetings"
                    ),
                    new OA\Property(
                        property: "content",
                        description: "Content of the message",
                        type: "string",
                        example: "Hello, group!"
                    ),
                    new OA\Property(
                        property: "user_id",
                        description: "ID of the user",
                        type: "integer",
                        example: 123
                    )
                ]
            )
        ),
        tags: ["Messages"],
        parameters: [
            new OA\Parameter(
                name: 'gid',
                description: 'ID of the group to send the message to',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Message sent successfully",
                content: new OA\JsonContent(ref: "#/components/schemas/Message")
            ),
            new OA\Response(
                response: 404,
                description: "Group not found",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "message",
                            description: "Error message",
                            type: "string",
                            example: "Group not found."
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
        $subject = $data['subject'] ?? '';
        $content = $data['content'] ?? '';
        $groupId = (int) $request->getAttributes()['id'];

        $user = $this->userService->getUser($data['user_id']);

        $group = $this->groupService->getGroup($groupId);
        if (!$group) {
            return $this->response->json($response, ['message' => 'Group not found.'])
                ->withStatus(StatusCodeInterface::STATUS_NOT_FOUND);
        }

        $message = $this->messageService->sendMessage($subject, $content, $group, $user);
        return $this->response->json($response, $message)
            ->withStatus(StatusCodeInterface::STATUS_CREATED);
    }

    #[OA\Get(
        path: "/groups/{id}/messages",
        summary: "Fetch all messages from a group",
        tags: ["Messages"],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID of the group to fetch messages from',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            ),
            new OA\Parameter(
                name: 'page',
                description: 'Page number for pagination',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer', default: 1)
            ),
            new OA\Parameter(
                name: 'limit',
                description: 'Number of messages per page',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer', default: 10)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "List of messages from the group",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(ref: "#/components/schemas/Message")
                )
            ),
            new OA\Response(
                response: 404,
                description: "Group not found",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "message",
                            description: "Error message",
                            type: "string",
                            example: "Group not found."
                        )
                    ]
                )
            )
        ]
    )]
    public function list(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $page = (int) ($request->getQueryParams()['page'] ?? 1);
        $limit = (int) ($request->getQueryParams()['limit'] ?? 10);
        $groupId = (int) $request->getAttributes()['id'];

        $group = $this->groupService->getGroup($groupId);
        if (!$group) {
            return $this->response->json($response, ['message' => 'Group not found.'])
                ->withStatus(StatusCodeInterface::STATUS_NOT_FOUND);
        }

        $messages = $this->messageService->fetchMessagesByGroupId($groupId, $page, $limit);
        return $this->response->json($response, $messages);
    }
}
