<?php

    namespace Powerhouse\Console\Traits;

    trait CommandsInfo
    {

        /**
         * The list of commands information.
         * 
         * @var array
         */
        protected $commands = [
            'create:controller' => [
                'argc' => 3
            ],
            'create:model' => [
                'argc' => 3
            ],
            'create:middleware' => [
                'argc' => 3
            ],
            'create:request' => [
                'argc' => 3
            ],
            'create:mail' => [
                'argc' => 3
            ],
            'create:serviceprovider' => [
                'argc' => 3
            ],
            'create:event' => [
                'argc' => 3
            ],
            'create:listener' => [
                'argc' => 3
            ],
            'delete:caches' => [
                'argc' => 3
            ],
            'create:auth' => [
                'argc' => 2
            ],
            'create:key' => [
                'argc' => 2
            ],
        ];

    }
