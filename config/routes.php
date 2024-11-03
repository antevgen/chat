<?php

declare(strict_types=1);

use App\Http\Controllers\Api\GroupController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\UserController;
use App\Middleware\ValidationMiddleware;
use Nyholm\Psr7\Response;
use OpenApi\Generator;
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
        $jsonResponse->getBody()->write($openapi->toJson());
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

        $apiGroup->group('/groups', function (RouteCollectorProxy $group) {

            $group->get('', [GroupController::class, 'list']);

            $group->post('', [GroupController::class, 'create']);

            $group->get('/{id}/messages', [MessageController::class, 'getMessagesByGroup']);
        });

        $apiGroup->group('/users', function (RouteCollectorProxy $group) use ($app) {
            $group->post('', [UserController::class, 'create'])
                ->add(
                    $app->getContainer()?->get(ValidationMiddleware::class)
                        ->setRules([
                            'username' => Assert::alnum()->noWhitespace()->length(3, 15),
                        ])
                );
        });

        $apiGroup->group('/messages', function (RouteCollectorProxy $messages) {
            $messages->post('/{group_id}', [MessageController::class, 'postMessage']);
        });
    });
};
