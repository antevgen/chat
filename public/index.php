<?php

require __DIR__ . '/vendor/autoload.php';

use Slim\Factory\AppFactory;
use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$app = AppFactory::create();
$app->addBodyParsingMiddleware(); // For JSON request bodies

// Register routes
require __DIR__ . '/../config/routes.php';

// Run the application
$app->run();
