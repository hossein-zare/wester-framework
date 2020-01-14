<?php

    namespace App\Transit\Http\Middleware;

    use Powerhouse\Interfaces\Middleware\MiddlewareInterface;
    
    class Auth implements MiddlewareInterface
    {

        /**
         * Authenticate the user on the current route
         *
         * @param   object  $request
         * @return  bool
         */
        public function handle($request)
        {
            // Authentication failed
            // The user will be redirected to the login route.
            if (auth()->verified() === false)
                redirect()->route('login')->do();

            return true;
        }

    }
