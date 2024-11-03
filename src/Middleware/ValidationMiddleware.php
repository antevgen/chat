<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Response\JsonResponse;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Respect\Validation\Exceptions\ValidationException;
use Respect\Validation\Validatable;

class ValidationMiddleware implements MiddlewareInterface
{
    /**
     * @var array<string, Validatable>
     */
    private array $rules = [];

    public function __construct(
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly JsonResponse $response,
    ) {
    }

    /**
     * @param  array<string, Validatable> $rules
     */
    public function setRules(array $rules): self
    {
        $this->rules = $rules;

        return $this;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $data = $request->getParsedBody();
        if (!is_array($data)) {
            return $handler->handle($request);
        }

        $errors = [];


        foreach ($this->rules as $field => $rule) {
            try {
                $rule->assert($data[$field] ?? null);
            } catch (ValidationException $exception) {
                $errors[$field] = $exception->getMessage();
            }
        }

        if (!empty($errors)) {
            $response = $this->responseFactory->createResponse(StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY);

            return $this->response->json($response, ['errors' => $errors]);
        }

        // Proceed to the next middleware or the route handler if no errors
        return $handler->handle($request);
    }
}
