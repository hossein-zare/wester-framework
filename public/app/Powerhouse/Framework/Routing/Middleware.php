<?php

    namespace Powerhouse\Routing;

    use Exception;
    use Powerhouse\Interfaces\Middleware\MiddlewareInterface;
    use Powerhouse\Routing\Route;
    use App\Transit\Http\Bridge;

    class Middleware extends Bridge
    {

        /**
         * Run a general group of middleware.
         *
         * @param  string|array  $request
         * @return closure
         */
        public function general($request)
        {
            ob_start();

            foreach ($this->middleware as $middlewareMethod) {
                $middleware = new $middlewareMethod();

                if (!($middleware instanceof MiddlewareInterface))
                    throw new Exception("The middleware isn't valid!");

                $trigger = $middleware->handle($request);
                
                if ($trigger !== true)
                    return false;
            }

            return true;
        }

        /**
         * Run a user-defined group of middleware.
         *
         * @param  string|array  $group
         * @return void
         */
        public function group($group, $request)
        {
            ob_start();

            // Kill the process if the group is empty
            if (empty($group))
                return true;

            // Levels of middleware
            $array = [];
            if (!is_array($group)) {
                if (isset($this->middlewareGroups[$group]))
                    $array = $this->middlewareGroups[$group];
                else
                    throw new Exception("The Middleware applied on this route doesn't exist!");
            } else
                foreach ($group as $single)
                    $array[] = $this->middlewareGroups[$single];


            foreach ($array as $middlewareMethod) {
                foreach ($middlewareMethod as $middleName) {
                    $middleware = new $middleName();

                    if (!($middleware instanceof MiddlewareInterface))
                        throw new Exception("The middleware isn't valid!");

                    $trigger = $middleware->handle($request);
                    
                    if ($trigger !== true)
                        return false;
                }
            }
            
            return true;
        }

    }
