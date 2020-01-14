<?php

    namespace Database\Connectors;

    use PDO;
    use Database\Cache;

    class Mysql extends Connector
    {

        /**
         * The default connection.
         * 
         * @var string
         */
        protected $defaultConnection;

        /**
         * Create a new instance of Mysql.
         * 
         * @param  string  $connection
         * @return void
         */
        public function __construct($connection)
        {
            $this->defaultConnection = $connection;
        }

        /**
         * Connect to the database.
         * 
         * @return \PDO
         */
        public function connect()
        {
            if (Cache::isConnected($this->defaultConnection, 'mysql') === false) {
                $db = $this->prepareConnection();
                $dsn = $this->getDsn($db);
                $pdo = $this->createConnection($dsn);
                $this->configureEncoding($pdo, $db);
                $this->configureTimezone($pdo, $db);

                // Cache the connection
                Cache::cacheConnection($this->defaultConnection, 'mysql', $pdo);
            }
        }

        /**
         * Get the Data Source Name.
         * 
         * @param  array  $db
         * @return string
         */
        private function getDsn(array $db)
        {
            $host = $db['host'];
            $dbname = $db['database'];
            $port = $this->getPort($db);

            return "mysql:host={$host};{$port}dbname={$dbname}";
        }

        /**
         * Get the database port.
         * 
         * @param  array  $db
         * @return string
         */
        private function getPort(array $db){
            if (isset($db['port']))
                return "port={$db['port']};";

            return;
        }

        /**
         * Configure database encoding.
         * 
         * @param  \PDO  $pdo
         * @param  array  $db
         * @return void
         */
        private function configureEncoding(PDO $pdo, array $db)
        {
            if (isset($db['charset']))
                $pdo->exec("set names '{$db['charset']}'". (isset($db['collation']) ? " collate '{$db['collation']}'": ''));
        }

        /**
         * Configure database timezone.
         * 
         * @param  \PDO  $pdo
         * @param  array  $db
         * @return void
         */
        private function configureTimezone(PDO $pdo, array $db)
        {
            if(isset($db['timezone']))
                $pdo->exec("set time_zone = '{$db['timezone']}'");
        }

    }
