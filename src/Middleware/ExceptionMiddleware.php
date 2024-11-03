<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Response\JsonResponse;
use DomainException;
use Fig\Http\Message\StatusCodeInterface;
use InvalidArgumentException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpException;
use Throwable;

class ExceptionMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly JsonResponse $response,
        private readonly ?LoggerInterface $logger = null,
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (Throwable $exception) {
            return $this->render($exception);
        }
    }

    private function render(
        Throwable $exception,
    ): ResponseInterface {
        $httpStatusCode = $this->getHttpStatusCode($exception);
        $response = $this->responseFactory->createResponse($httpStatusCode);

        // Log error
        if (isset($this->logger)) {
            $this->logger->error(
                sprintf(
                    '%s;Code %s;File: %s;Line: %s',
                    $exception->getMessage(),
                    $exception->getCode(),
                    $exception->getFile(),
                    $exception->getLine()
                ),
                $exception->getTrace()
            );
        }

        return $this->renderJson($exception, $response);
    }

    public function renderJson(Throwable $exception, ResponseInterface $response): ResponseInterface
    {
        $data = [
            'error' => [
                'message' => $exception->getMessage(),
            ],
        ];

        return $this->response->json($response, $data);
    }

    private function getHttpStatusCode(Throwable $exception): int
    {
        $statusCode = StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR;

        if ($exception instanceof HttpException) {
            $statusCode = $exception->getCode();
        }

        if ($exception instanceof DomainException || $exception instanceof InvalidArgumentException) {
            $statusCode = StatusCodeInterface::STATUS_BAD_REQUEST;
        }

        return $statusCode;
    }
}
