<?php

declare(strict_types=1);

use App\Http\Controllers\Api\GroupController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\UserController;
use App\Middleware\UserExistsMiddleware;
use App\Middleware\ValidationMiddleware;
use App\Repository\UserRepository;
use App\Response\JsonResponse;
use Nyholm\Psr7\Response;
use OpenApi\Generator;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\App;
use Slim\Psr7\Request;
use Slim\Routing\RouteCollectorProxy;
use Respect\Validation\Validator as Assert;

return static function (App $app) {
    $app->get('/docs/openapi.json', function (Request $request, Response $response) {
        // Scan your project directory for OpenAPI attributes/annotations
        $openapi = Generator::scan([__DIR__ . '/../src']);

        // Convert OpenAPI object to JSON.
        $jsonResponse = $response->withHeader('Content-Type', 'application/json');
        $jsonResponse->getBody()->write($openapi ? $openapi->toJson() : '');
        return $jsonResponse;
    });

    // Redirect the main page to /swagger-ui
    $app->get('/', function (Request $request, Response $response) {
        return $response
            ->withHeader('Location', '/api')
            ->withStatus(301);
    });

    $app->group('/api', function (RouteCollectorProxy $apiGroup) use ($app) {
        $apiGroup->get('', function (Request $request, Response $response) {
            return $response
                ->withHeader('Location', '/swagger-ui/')
                ->withStatus(301);
        });

        $apiGroup->group('/groups', function (RouteCollectorProxy $group) use ($app) {

            $group->get('', [GroupController::class, 'list']);

            $group->post('', [GroupController::class, 'create'])
                ->add(ValidationMiddleware::createWithRules(
                    $app->getContainer()?->get(ResponseFactoryInterface::class),
                    $app->getContainer()?->get(JsonResponse::class),
                    ['name' => Assert::notBlank()->length(3, 55)]
                ));

            $group->post('/{id}/members', [GroupController::class, 'join'])
                ->add(new UserExistsMiddleware(
                    $app->getContainer()?->get(UserRepository::class),
                    $app->getContainer()?->get(JsonResponse::class),
                    $app->getContainer()?->get(ResponseFactoryInterface::class),
                ));

            $group->get('/{id}/messages', [MessageController::class, 'getMessagesByGroup']);
        });

        $apiGroup->group('/users', function (RouteCollectorProxy $group) use ($app) {
            $group->get('', [UserController::class, 'list']);
            $group->post('', [UserController::class, 'create'])
                ->add(ValidationMiddleware::createWithRules(
                    $app->getContainer()?->get(ResponseFactoryInterface::class),
                    $app->getContainer()?->get(JsonResponse::class),
                    [
                        'username' => Assert::alnum()->noWhitespace()->length(3, 15),
                        'email' => Assert::email()->length(1, 255),
                    ]
                ));
        });

        $apiGroup->group('/messages', function (RouteCollectorProxy $messages) {
            $messages->post('/{group_id}', [MessageController::class, 'postMessage']);
        });
    });
};
