<?php

    namespace Powerhouse\Routing\Traits;

    use App\Transit\Http\Handler\Request;
    use Powerhouse\Routing\Middleware;

    trait MiddlewareParser
    {

        /**
         * Parse the middleware.
         *
         * @param  string|array  $group
         * @return void|bool
         */
        public function parseMiddleware($group = null)
        {
            $request = $GLOBALS['structure']['request'];

            $middleware = new Middleware();

            // General
            $response = $middleware->general($request);

            // Once it returns false it avoids the current route.
            if ($response !== true)
                return false;

            // Group
            if ($group !== null) {
                $response = $middleware->group($group, $request);

                // Once it returns false it avoids the current route.
                if ($response !== true)
                    return false;
            }
        }

    }
