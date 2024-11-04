<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Repository\UserRepository;
use App\Response\JsonResponse;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class UserExistsMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly JsonResponse $response,
        private ResponseFactoryInterface $responseFactory,
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /** @var array<string, mixed> $data */
        $data = $request->getParsedBody();
        $userId = $data['user_id'] ?? null;

        if ($userId === null || !$this->userRepository->find((int) $userId)) {
            $response = $this->responseFactory->createResponse(StatusCodeInterface::STATUS_NOT_FOUND);

            return $this->response->json($response, ['error' => 'User not found.']);
        }

        return $handler->handle($request);
    }
}
