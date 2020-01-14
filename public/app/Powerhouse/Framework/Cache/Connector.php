<?php

    namespace Powerhouse\Cache;

    use Exception;

    abstract class Connector extends Connections
    {

        /**
         * Available connectors.
         * 
         * @var array
         */
        protected $connectors = [
            'memcached' => \Powerhouse\Cache\Drivers\Memcached\Connector::class,
            'memcache' => \Powerhouse\Cache\Drivers\Memcache\Connector::class,
        ];

        /**
         * Connect to a cache server.
         * 
         * @param  string  $name
         * @return void
         */
        protected function connect($name)
        {
            if (!isset(static::$connections[$name])) {
                $config = $this->getConfig($name);
                $driver = $config['driver'];

                return static::$connections[$name] = $this->grabConnector($driver)->connect($config);
            } else {
                return static::$connections[$name];
            }
        }

        /**
         * Grab the connector and create a new instance of it.
         * 
         * @param  string  $driver
         * @return \Memcached
         */
        protected function grabConnector($driver)
        {
            $connector = $this->connectors[$driver];
            return (new $connector());
        }

        /**
         * Get the configuration.
         * 
         * @param  string  $name
         * @return array
         */
        protected function getConfig($name)
        {
            global $config_cache;
            return $config_cache['connections'][$name];
        }
    }
