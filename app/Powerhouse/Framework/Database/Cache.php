<?php

    namespace Database;

    use PDO;

    class Cache
    {

        /**
         * Database Connection.
         * 
         * @var array
         */
        protected static $conn = [];

        /**
         * Cache the database connection.
         * 
         * @param  string  $connection
         * @param  string  $driver
         * @param  \PDO  $pdo
         * @return bool
         */
        public static function cacheConnection(string $connection, string $driver, PDO $pdo)
        {
            if (! isset(self::$conn[$driver][$connection]) || self::$conn[$driver][$connection] === null) {
                self::$conn[$driver][$connection] = $pdo;
                return true;
            }

            return false;
        }

        /**
         * Get the connection.
         * 
         * @param  string  $connection
         * @param  string  $driver
         * @return \PDO
         */
        public static function getConnection(string $connection = null, string $driver = null)
        {
            global $config_db;

            if ($connection === null && $driver === null) {
                $connection = $config_db['default'];
                $driver = $config_db['connections'][$connection]['driver'];
            }

            return isset(self::$conn[$driver][$connection]) ? self::$conn[$driver][$connection] : null;
        }

        /**
         * Connection status.
         * 
         * @param  string  $connection
         * @param  string  $driver
         * @param bool
         */
        public static function isConnected(string $connection = null, string $driver = null)
        {
            if (self::getConnection($connection, $driver) === null)
                return false;
            return true;
        }

        /**
         * Destruct the current connection.
         * 
         * @param  string  $connection
         * @param  string  $driver
         * @return void
         */
        public static function destructConnection(string $connection, string $driver)
        {
            static::$conn[$driver][$connection] = null;
        }

        /**
         * Destruct all of the active connections.
         * 
         * @return void
         */
        public static function destructAll()
        {
            static::$conn = null;
        }

    }
