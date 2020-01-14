<?php

    namespace App\Transit\Http\Handler;

    abstract class HttpConfiguration
    {

        /**
         * Never flash the following inputs
         *
         * @var array
         */
        protected $dontFlash = [
            'password',
            'password_confirmation',
        ];

    }
