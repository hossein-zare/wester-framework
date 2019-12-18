<?php

    namespace Database\Traits;

    use Database\Cache;

    trait ConnectorFinder
    {

        /**
         * The default connection.
         * 
         * @var string
         */
        protected $defaultConnection;

        /**
         * The default driver.
         * 
         * @var string
         */
        protected $driver;

        /**
         * Database connectors.
         */
        protected $connectors = [
            'mysql' => \Database\Connectors\Mysql::class
        ];

        /**
         * Create a connector.
         * 
         * @param  string  $connection
         * @return \Database\Connectors\...
         */
        public function createConnector($connection = null)
        {   
            return new $this->connectors[$this->driver]($this->defaultConnection);
        }

        /**
         * Set the connection info.
         * 
         * @param  string  $connection
         * @return void
         */
        public function setConnection($connection = null)
        {
            global $config_db;
            
            if ($connection === null)
                $connection = $config_db['default'];

            // Store the default connection name
            $this->defaultConnection = $connection;

            // Store the default database driver
            $this->driver = $config_db['connections'][$connection]['driver'];
        }

    }
