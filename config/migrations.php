<?php

declare(strict_types=1);

return [
    'table_storage' => [
        'table_name' => 'doctrine_migration_versions',
    ],
    'migrations_paths' => [
        'Migrations' => __DIR__ . '/../migrations', // Path to the migrations directory
    ],
];
