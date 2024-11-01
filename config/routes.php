<?php

declare(strict_types=1);

use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\MessageController;

return function (App $app) {
    // Group Routes
    $app->group('/groups', function (RouteCollectorProxy $group) {

        // Get list of all groups
        $group->get('', [GroupController::class, 'getAllGroups']);  // GET /groups

        // Create a new group
        $group->post('', [GroupController::class, 'createGroup']);  // POST /groups

        // Get all messages within a specific group
        $group->get('/{id}/messages', [MessageController::class, 'getMessagesByGroup']);  // GET /groups/{id}/messages
    });

    // Message Routes
    $app->group('/messages', function (RouteCollectorProxy $messages) {

        // Post a new message to a group
        $messages->post('/{group_id}', [MessageController::class, 'postMessage']);  // POST /messages/{group_id}
    });
};
