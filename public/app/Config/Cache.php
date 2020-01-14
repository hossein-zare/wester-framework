<?php

    $config_cache = [];

    /**
     * Default Connection
     */
    $config_cache['default'] = 'memcache';

    /**
     * Memcached Configuration
     */
    $config_cache['connections'] = [
        'memcached' => [
            'driver' => 'memcached',
            'sasl' => [
                //'username', 'password'
            ],
            'servers' => [
                [
                    'host' => 'localhost',
                    'port' => 11211,
                    'memory' => 100
                ],
            ],
            'options' => [
                // Memcached::OPT_CONNECT_TIMEOUT => 2000,
            ],
            'persistent_id' => null,
        ],

        'memcache' => [
            'driver' => 'memcache',
            'servers' => [
                [
                    'host' => 'localhost',
                    'port' => 11211,
                    'memory' => 100
                ],
            ],
            'persistent_connection' => false
        ]
    ];
