<?php

    namespace Powerhouse\Cache\Drivers\Memcached;

    use Memcached;

    class Connector
    {

        /**
         * Connect to memcached.
         * 
         * @param  array  $config
         * @return \Memcached
         */
        public function connect(array $config)
        {
            $servers = $config['servers'];
            $persistent_id = $config['persistent_id'];
            $options = $config['options'];
            $credentials = $config['sasl'];

            return $this->getConnection(
                $servers, $persistent_id, $options, $credentials
            );
        }

        /**
         * Get the memcached instance.
         * 
         * @param  array  $servers
         * @param  string|null  $persistent_id
         * @param  array  $options
         * @param  array  $credentials
         * @return void
         */
        protected function getConnection(array $servers, $persistent_id = null, array $options = [], array $credentials = [])
        {
            $memcached = $this->getInstance($persistent_id);

            $this->setCredentials($memcached, $credentials);
            $this->setOptions($memcached, $options);
            $this->addServers($memcached, $servers);

            return $memcached;
        }

        /**
         * Create a memcached instance.
         * 
         * @param  string|null  $persistent_id
         * @return \Memcached
         */
        protected function getInstance($persistent_id = null)
        {
            return empty($persistent_id) ? new Memcached() : new Memcached($persistent_id);
        }

        /**
         * Set the sasl credentials.
         * 
         * @param  \Memcached  $memcached
         * @param  array  $credentials
         * @return void
         */
        protected function setCredentials($memcached, array $credentials = [])
        {
            if (count($credentials) === 2) {
                [$username, $password] = $credentials;
                $memcached->setOption(Memcached::OPT_BINARY_PROTOCOL, true);
                $memcached->setSaslAuthData($username, $password);
            }
        }

        /**
         * Set the options.
         * 
         * @param  \Memcached  $memcached
         * @param  array  $options
         * @return void
         */
        protected function setOptions($memcached, array $options = [])
        {
            if (count($options)) {
                $memcached->setOptions($options);
            }
        }

        /**
         * Add servers.
         * 
         * @param  \Memcached  $memcached
         * @param  array  $servers
         * @return void
         */
        protected function addServers($memcached, array $servers)
        {
            if (! $memcached->getServerList())
                foreach ($servers as $server) {
                    $memcached->addServer(
                        $server['host'], $server['port'], $server['memory']
                    );
                }
        }

    }
