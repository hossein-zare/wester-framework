<?php

    namespace Powerhouse\Castles;

    use Powerhouse\Foundation\Castle\Castle;

    class Curl extends Castle
    {

        /**
         * Get the registered ancestor.
         *
         * @return string
        */
        protected static function getCastleAncestor()
        {
            return 'AppBundles\Curl\Curl';
        }

    }
