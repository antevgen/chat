<?php

declare(strict_types=1);

use App\Middleware\ExceptionMiddleware;
use Slim\App;

return static function (App $app) {
    $app->addBodyParsingMiddleware();
    $app->addRoutingMiddleware();
    $app->add(ExceptionMiddleware::class);
};
