<?php

    namespace App\Transit\Providers;

    class ServiceContainer
    {

        /**
         * Opening services that are supposed to run before anything else.
         * It may be necessory to mention that the order of the services
         * affects the performance of the application.
         *
         * @var array
         */
        protected $opening = [
            \App\Transit\Providers\Services\SecurityServiceProvider::class,
            \App\Transit\Providers\Services\SessionServiceProvider::class,
            \App\Transit\Providers\Services\RouteServiceProvider::class,
            \App\Transit\Providers\Services\AuthServiceProvider::class,
            \App\Transit\Providers\Services\AppServiceProvider::class,
            \App\Transit\Providers\Services\ViewServiceProvider::class,
            \App\Transit\Providers\Services\EventServiceProvider::class,
        ];

        /**
         * Closing services that are supposed to run at the end.
         * It may be necessory to mention that the order of the services
         * affects the performance of the application.
         *
         * @var array
         */
        protected $closing = [
            //
        ];

        /**
         * Provider Groups
         * Groups of services that belong to various sections of the application.
         *
         * @var array
         */
        protected $groups = [
            //
        ];

    }
