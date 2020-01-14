<?php

    namespace Powerhouse\Castles;

    use Powerhouse\Foundation\Castle\Castle;

    class View extends Castle
    {

        /**
         * Get the registered ancestor.
         *
         * @return string
        */
        protected static function getCastleAncestor()
        {
            return 'Powerhouse\View\View';
        }

    }
