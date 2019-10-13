<?php

    namespace App\Transit\Http\Middleware;

    use Powerhouse\Interfaces\Middleware\MiddlewareInterface;
    use Powerhouse\Foundation\Middleware\StringTrimmer as Middleware;

    class StringTrimmer extends Middleware implements MiddlewareInterface
    {

        /**
         * A list of fields that should be ignored
         * 
         * @var array
         */
        protected $except = [
            'password',
            'password_confirmation'
        ];

    }
