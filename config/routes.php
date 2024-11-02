<?php

declare(strict_types=1);

use App\Http\Controllers\Api\GroupController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\UserController;
use OpenApi\Generator;
use Slim\App;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Slim\Routing\RouteCollectorProxy;

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

    $app->group('/api', function (RouteCollectorProxy $apiGroup) {
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

        $apiGroup->group('/users', function (RouteCollectorProxy $group) {
            $group->post('', [UserController::class, 'create']);
        });

        $apiGroup->group('/messages', function (RouteCollectorProxy $messages) {
            $messages->post('/{group_id}', [MessageController::class, 'postMessage']);
        });
    });
};
