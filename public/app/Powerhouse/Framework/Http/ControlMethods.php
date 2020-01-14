<?php

    namespace Powerhouse\Http;

    use App\Transit\Http\Handler\Request;

    class ControlMethods
    {

        /**
         * Get the method and call it.
         * 
         * @param  string  $controller
         * @param  \App\Transit\Http\Handler\Request  $request
         * @return mixed
         */
        public static function catch($controller, Request $request)
        {
            $path = "\\App\\Transit\\Http\\Controllers\\{$controller}";

            $obj = new $path();
            $method = $request->method();
            if (method_exists($obj, $method))
                return $obj->{$request->method()}($request);
                
            abort(404);
        }

    }
