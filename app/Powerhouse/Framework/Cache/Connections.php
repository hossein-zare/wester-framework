<?php

    namespace Powerhouse\Cache;

    abstract class Connections extends Manager
    {

        /**
         * The list of established connections.
         * 
         * @var array
         */
        protected static $connections = [];

        /**
         * Connect to the current connection.
         * 
         * @return  object
         */
        protected function conn()
        {
            $defaultConnection = $this->defaultConnection;
            return $this->connect($defaultConnection);
        }

    }
