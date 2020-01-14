<?php

    namespace Powerhouse\Support\Extendable;

    class Spark
    {

        /**
         * The list of spark directives.
         *
         * @var string
         */
        protected static $directives = [];

        /**
         * The spark directive.
         *
         * @param  string  $name
         * @param  callback  $callback
         * @return void
         */
        public static function directive($name, $callback)
        {
            self::$directives[$name] = $callback;
        }
        
        /**
         * Get the directives.
         *
         * @return array
         */
        public function getDirectives()
        {
            return static::$directives;
        }

    }
