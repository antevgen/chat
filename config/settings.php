<?php

declare(strict_types=1);

use Psr\Log\LogLevel;

return [
    'error' => [
        'display_error_details' => $_ENV['DISPLAY_ERROR'] ?? true,
    ],

    'logger' => [
        // Log file location
        'path' => __DIR__ . '/../' . ($_ENV['LOG_DIR'] ?? 'logs'),
        // Default log level
        'level' => LogLevel::DEBUG,
    ],

    'doctrine' => [
        'dev_mode' => filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN),
        'cache_dir' => __DIR__ . '/../var/doctrine',
        'metadata_dirs' => [__DIR__ . '/../src/Entity'],
        'connection' => [
            'driver' => 'pdo_sqlite',
            'path' => __DIR__ . '/../' . $_ENV['DB_PATH'],
        ]
    ]
];
