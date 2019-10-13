<?php

    $config_db = [];

    /**
     * Default Connection
     */
    $config_db['default'] = 'mysql';

    /**
     * Database Settings
     */
    $config_db['connections'] = [
        'mysql' => [
            'driver' => 'mysql',
            'host' => 'localhost',
            'port' => '3306',
            'database' => 'wester',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            //'timezone' => 'US/Central'
        ],
    ];
