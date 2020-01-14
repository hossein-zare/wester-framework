<?php

    namespace Database\Connectors;

    use PDO;
    use PDOException;
    use Exception;
    use Powerhouse\Console\ConsoleApplication;

    abstract class Connector
    {

        /**
         * Connection info.
         * 
         * @var array
         */
        protected $db = [];

        /**
         * Prepare a new connection.
         * 
         * @return array
         */
        protected function prepareConnection(){
            global $config_db;
            
            $this->db = [
                'default' => $this->defaultConnection,
                'info' => $config_db['connections'][$this->defaultConnection]
            ];

            return $this->db['info'];
        }

        /**
         * Create a PDO Connection.
         * 
         * @param  string  $dsn
         * @return PDO
         */
        public function createConnection($dsn)
        {
            try {
                $db = $this->db['info'];
                list($username, $password) = [$db['username'], $db['password']];
                $conn = new PDO($dsn, $username, $password, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
                ]);

                return $conn;
            } catch (PDOException $e) {
                if (CONSOLE === true) {
                    $console = new ConsoleApplication();
                    $console->shutdown("Please check your database connection!", "red");
                } else
                    throw new Exception($e->getMessage());
            }
        }

    }
