<?php

declare(strict_types=1);

return [
    'settings' => [
        'slim' => [
            'displayErrorDetails' => true,
            'logErrors' => true,
            'logErrorDetails' => true,
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
    ]
];
