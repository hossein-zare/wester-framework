<?php

    namespace Powerhouse\Support\Extendable;

    class Validator
    {

        /**
         * The list of the validator extensions.
         *
         * @var string
         */
        protected static $extensions = [];

        /**
         * The validator extending method.
         *
         * @param  string  $name
         * @param  callback  $callback
         * @return void
         */
        public static function extend($name, $callback)
        {
            self::$extensions[$name] = $callback;
        }
        
        /**
         * Get the extensions.
         *
         * @return varray
         */
        public static function getExtensions()
        {
            return self::$extensions;
        }

    }
