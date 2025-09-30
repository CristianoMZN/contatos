<?php

return
[
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/migrations',
        'seeds' => '%%PHINX_CONFIG_DIR%%/db/seeds'
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => 'development',
        'production' => [
            'adapter' => 'mysql',
            'host' => getenv('DB_HOST') ?: 'localhost',
            'name' => getenv('DB_NAME') ?: 'production_db',
            'user' => getenv('DB_USER') ?: 'root',
            'pass' => getenv('DB_PASSWORD') ?: '',
            'port' => getenv('DB_PORT') ?: '3306',
            'charset' => 'utf8mb4',
        ],
        'development' => [
            'adapter' => 'mysql',
            'host' => getenv('DB_HOST') ?: 'localhost',
            'name' => getenv('DB_NAME') ?: 'development_db',
            'user' => getenv('DB_USER') ?: 'root',
            'pass' => getenv('DB_PASSWORD') ?: '',
            'port' => getenv('DB_PORT') ?: '3306',
            'charset' => 'utf8mb4',
        ],
        'testing' => [
            'adapter' => 'mysql',
            'host' => getenv('DB_HOST') ?: 'localhost',
            'name' => getenv('DB_NAME') ?: 'testing_db',
            'user' => getenv('DB_USER') ?: 'root',
            'pass' => getenv('DB_PASSWORD') ?: '',
            'port' => getenv('DB_PORT') ?: '3306',
            'charset' => 'utf8mb4',
        ]
    ],
    'version_order' => 'creation'
];
