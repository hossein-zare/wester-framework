<?php

    namespace Powerhouse\Freeway;

    class NoMethods
    {

        /**
         * {@inheritdoc}
         */
        public function __construct()
        {
            //
        }

        /**
         * {@inheritdoc}
         */
        public function __call($name, $args)
        {
            return $this;
        }

    }
