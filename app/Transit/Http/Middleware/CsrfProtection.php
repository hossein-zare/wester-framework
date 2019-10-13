<?php

    namespace App\Transit\Http\Middleware;

    use Powerhouse\Interfaces\Middleware\MiddlewareInterface;
    use Powerhouse\Foundation\Middleware\CsrfProtection as Middleware;

    class CsrfProtection extends Middleware implements MiddlewareInterface
    {

        /**
         * A list of routes that should be ignored for protection
         * 
         * @var array
         */
        protected $except = [
            //
        ];

    }
