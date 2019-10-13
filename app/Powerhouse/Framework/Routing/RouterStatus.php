<?php

    namespace Powerhouse\Routing;

    class RouterStatus
    {

        /**
         * Increase the incremenet.
         * 
         * @return void
         */
        public static function increase()
        {
            $GLOBALS['structure']['routerStatus']++;
        }

        /**
         * Get the increment.
         * 
         * @return int
         */
        public static function get()
        {
            return $GLOBALS['structure']['routerStatus'];
        }

    }
