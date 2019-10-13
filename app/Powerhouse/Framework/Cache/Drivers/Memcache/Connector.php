<?php

    namespace Powerhouse\Cache\Drivers\Memcache;

    use Memcache;

    class Connector
    {

        /**
         * Connect to memcache.
         * 
         * @param  array  $config
         * @return \Memcache
         */
        public function connect(array $config)
        {
            $servers = $config['servers'];
            $persistent_connection = $config['persistent_connection'];

            return $this->getConnection(
                $servers, $persistent_connection
            );
        }

        /**
         * Get the memcache instance.
         * 
         * @param  array  $servers
         * @param  string|null  $persistent_connection
         * @return void
         */
        protected function getConnection(array $servers, $persistent_connection = false)
        {
            $memcache = $this->getInstance();

            $this->addServers($memcache, $servers, $persistent_connection);

            return $memcache;
        }

        /**
         * Create a memcache instance.
         * 
         * @return \Memcache
         */
        protected function getInstance()
        {
            return new Memcache();
        }

        /**
         * Add servers.
         * 
         * @param  \Memcache  $memcache
         * @param  array  $servers
         * @param  string|null  $persistent_connection
         * @return void
         */
        protected function addServers($memcache, array $servers, $persistent_connection)
        {
            foreach ($servers as $server) {
                $memcache->addServer(
                    $server['host'], $server['port'], $persistent_connection, $server['memory']
                );
            }
        }

    }
