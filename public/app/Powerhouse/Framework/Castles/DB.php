<?php

    namespace Powerhouse\Castles;

    use Powerhouse\Foundation\Castle\Castle;

    class DB extends Castle
    {

        /**
         * Get the registered ancestor.
         *
         * @return string
        */
        protected static function getCastleAncestor()
        {
            return 'Database\DB';
        }

        /**
         * Apply the constructor methods on the ancestor.
         * 
         * @param  string  $method
         * @param  array  $args
         * @return mixed
         */
        protected static function constructMethods($method, $args)
        {
            if ($method === 'connection')
                return $args[0];
            return false;
        }

    }
