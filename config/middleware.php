<?php

declare(strict_types=1);

use App\Middleware\ExceptionMiddleware;
use App\Middleware\ValidationMiddleware;
use Slim\App;

return static function (App $app) {
    $app->add(ValidationMiddleware::class);
    $app->addBodyParsingMiddleware();
    $app->addRoutingMiddleware();
    $app->add(ExceptionMiddleware::class);
};
