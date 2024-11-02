<?php

declare(strict_types=1);

use Dotenv\Dotenv;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__. '/../');
$dotenv->load();

// Register dependencies
require __DIR__ . '/../config/dependencies.php';

// Create and configure Slim app
$app = AppFactory::create();
$app->addBodyParsingMiddleware();

// Register routes
(require __DIR__ . '/../config/routes.php')($app);

return $app;
