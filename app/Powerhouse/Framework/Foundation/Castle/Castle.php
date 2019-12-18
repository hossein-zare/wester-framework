<?php

    namespace Powerhouse\Foundation\Castle;

    class Castle
    {

        /**
         * Apply the constructor methods on the ancestor.
         * 
         * @param  string  $method
         * @param  array  $args
         * @return mixed
         */
        protected static function constructMethods($method, $args)
        {
            return false;
        }

        /**
         * Create a new instance of the castle.
         * 
         * @param  string  $method
         * @param  array  $args
         * @return closure
         */
        public function __call($method, $args)
        {
            $instance = static::getCastleAncestor();
            $construct = static::constructMethods($method, $args);

            if ($construct !== false)
                return (new $instance($args[0]));
            else
                return (new $instance())->$method(...$args);
        }

        /**
         * Create a new instance of the castle.
         * 
         * @param  string  $method
         * @param  array  $args
         * @return closure
         */
        public static function __callStatic($method, $args)
        {
            $instance = static::getCastleAncestor();
            $construct = static::constructMethods($method, $args);

            if ($construct !== false)
                return (new $instance($args[0]));
            else
                return (new $instance())->$method(...$args);
        }

    }
