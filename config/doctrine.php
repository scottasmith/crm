<?php

return [
    'default' => env('DB_CONNECTION', 'mysql'),

    'connections' => [
        'mysql' => [
            // Main connection configuration
            'driver' => 'pdo_mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'username' => env('DB_USERNAME', 'user'),
            'password' => env('DB_PASSWORD', ''),
            'database' => env('DB_DATABASE', 'test'),

            // Set the connection logger
            // 'logger' => \ScottSmith\Doctrine\Logger\PsrLogger::class,

            // Entity paths
            'paths' => [
                'app/Entities',
            ],

            // Migrations
            'migration_paths' => [
                'App\DoctrineMigrations' => getcwd() . '/database/doctrine_migrations/mysql',
            ],
        ],

        'mysql_tests' => [
            'driver' => 'pdo_mysql',
            'host' => env('TESTS_DB_HOST', 'mysql'),
            'port' => env('TESTS_DB_PORT', '3306'),
            'username' => env('TESTS_DB_USERNAME', 'tests'),
            'password' => env('TESTS_DB_PASSWORD', 'tests'),
            'database' => env('TESTS_DB_DATABASE', 'crm_tests'),

            // Entity paths
            'paths' => [
                'app/Entities',
            ],

            // Migrations
            'migration_paths' => [
                'App\DoctrineMigrations' => getcwd() . '/database/doctrine_migrations/mysql',
            ],
        ],
    ],

    'types' => [
        \Ramsey\Uuid\Doctrine\UuidType::NAME => \Ramsey\Uuid\Doctrine\UuidType::class,
    ],

    'cache' => (env('APP_ENV') == 'production')
        ? new \ScottSmith\Doctrine\Caching\FilesystemCachingProvider(getcwd() . '/storage/doctrine/cache')
        : null,

    // This is used by the doctrine commands so can't use laravel to locate the database directory
    'proxies' => (env('APP_ENV') === 'production') ? (getcwd() . '/storage/doctrine/proxies') : null,
];
