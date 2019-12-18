<?php

    namespace Powerhouse\Routing;
    
    use Exception;
    use App\Transit\Http\Handler\Request;
    use Powerhouse\Routing\View;
    
    abstract class Callback extends View
    {

        /**
         * Call the closure|method of the route.
         *
         * @param  array  $route
         * @return callback|string
         */
        protected function callback($route)
        {
            $request = request();
            
            $route = (Object) $route;
            if (is_callable($route->action))

                return $this->callCallback($route->action, $route->parameters, $request);

            else

                if (strpos($route->action, '@') !== false) {

                    $controller = explode('@', $route->action);
                    if ($controller[0] && $controller[1]) {
                        $namespace = $route->namespace;
                        $namespace = ($namespace !== null && $namespace !== '') ? $namespace . '\\' : '';
                        $path = 'App\\Transit\\Http\\Controllers\\'. $namespace . $controller[0];

                        return $this->callMethod($path, $controller, $route->parameters, $request);
                    } else
                        throw new Exception("Invalid controller has been given to the route!");
                        
                } else
                    throw new Exception("The controller and its method must be separated by @!");
        }
        
        /**
         * Call the callback.
         *
         * @param  callback|closure  $callback
         * @param  array  $args
         * @param  \App\Transit\Http\Request  $request
         * @return callback|string
         */
        private function callCallback($callback, $args, Request $request)
        {
            $function = new \ReflectionFunction($callback);
            $params = $function->getParameters();

            // Saves the position
            $position = null;

            // Find the position of the Request
            for ($i = 0; $i < count($params); $i++)
                if ($paramClass = $params[$i]->getClass()) {
                    if ($paramClass->name === 'App\Transit\Http\Handler\Request') {

                        $position = $i;
                        break;

                    } else {

                        // Custom requests
                        $customRequest = new $paramClass->name();
                        if ($customRequest->process() === true) {
                            $position = $i;
                            break;
                        } else
                            die('false');

                    }
                }

            // Insert the Request into the array
            if ($position !== null) {
                if(isset($customRequest))
                    $request = $customRequest;

                array_splice($args, $position, 0, array($request));
            }

            return $callback(...$args);
        }
        
        /**
         * Call the method.
         *
         * @param  string  $path
         * @param  array  $controller
         * @param  array  $args
         * @param  \App\Transit\Http\Request  $request
         * @return callback|string
         */
        private function callMethod($path, $controller, $args, Request $request)
        {
            $obj = new $path();

            $class = new \ReflectionClass($obj);
            $method = $class->getMethod($controller[1]);
            $params = $method->getParameters();

            // Saves the position
            $position = null;

            // Find the position of the Request
            for ($i = 0; $i < count($params); $i++)
                if ($paramClass = $params[$i]->getClass()) {
                    if ($paramClass->name === 'App\Transit\Http\Handler\Request') {

                        $position = $i;
                        break;

                    } else {

                        // Custom requests
                        $customRequest = new $paramClass->name();
                        if ($customRequest->process() === true) {
                            $position = $i;
                            break;
                        } else
                            die('false');

                    }
                }

            // Insert the Request into the array
            if ($position !== null) {
                if(isset($customRequest))
                    $request = $customRequest;

                array_splice($args, $position, 0, array($request));
            }

            return $obj->{$controller[1]}(...$args);
        }
    
    }
