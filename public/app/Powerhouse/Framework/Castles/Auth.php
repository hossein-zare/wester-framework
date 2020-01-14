<?php

    namespace Powerhouse\Castles;

    use Powerhouse\Foundation\Castle\Castle;

    class Auth extends Castle
    {

        /**
         * Get the registered ancestor.
         *
         * @return string
        */
        protected static function getCastleAncestor()
        {
            return 'Powerhouse\Auth\Auth';
        }

    }
