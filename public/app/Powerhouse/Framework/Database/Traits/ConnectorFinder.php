<?php

    namespace Database\Traits;

    use Exception;
    use Database\Cache;
    use Powerhouse\Console\ConsoleApplication;

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
            if (! $this->driver)
                throw new Exception("Please choose a database connection.");

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

            if (! $connection) {
                 if (CONSOLE === true) {
                    $console = new ConsoleApplication();
                    $console->shutdown("Please choose a database connection!", "red");
                } else
                    throw new Exception("Please choose a database connection.");
            }

            // Store the default database driver
            $this->driver = $config_db['connections'][$connection]['driver'];
        }

    }
