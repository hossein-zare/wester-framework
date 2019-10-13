<?php

    namespace Powerhouse\View;

    use Powerhouse\View\Spark;

    class Make
    {

        /**
         * Create a new view.
         * 
         * @return string
         */
        public static function make($vars, $view, $arguments = [])
        {
            $spark = new Spark();
            $spark->config($view, array_merge($vars, $arguments));
            return $spark->getRendered();
        }

    }