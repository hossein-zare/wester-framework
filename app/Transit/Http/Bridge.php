<?php

    namespace App\Transit\Http;

    class Bridge
    {

        /**
         * Global middleware stack.
         * These are supposed to be parsed with every request.
         *
         * @var array
         */
        protected $middleware = [
            \App\Transit\Http\Middleware\StringTrimmer::class,
            \App\Transit\Http\Middleware\EmptyStringNuller::class,
            \App\Transit\Http\Middleware\CsrfProtection::class,
        ];

        /**
         * Middleware groups.
         * These are almost multifunctional.
         *
         * @var array
         */
        protected $middlewareGroups = [
            'api' => [
                \App\Transit\Http\Middleware\APIGateway::class
            ],
            'auth' => [
                \App\Transit\Http\Middleware\Auth::class
            ]
        ];

    }
