<?php

    namespace Powerhouse\Cache;

    class Cache extends Connector
    {

        /**
         * The default cache store.
         * 
         * @var string
         */
        protected $defaultConnection;

        /**
         * The default cache driver.
         * 
         * @var string
         */
        protected $driver;

        /**
         * Create a new cache instance.
         * 
         * @return void
         */
        public function __construct()
        {
            $this->setConnection();
        }

        /**
         * Set default connection.
         * 
         * @param  string  $name
         * @return void
         */
        protected function setConnection($name = null)
        {
            global $config_cache;

            $default = $config_cache['default'];
            $connection = $name === null ? $default : $name;

            $this->defaultConnection = $connection;
            $this->driver = $this->getConfig($default)['driver'];
        }

    }
