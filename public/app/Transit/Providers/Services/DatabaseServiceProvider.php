<?php

    namespace App\Transit\Providers\Services;

    use Database\Traits\ConnectorFinder;
    use Powerhouse\Interfaces\ServiceProvider\ServiceProviderInterface;

    class DatabaseServiceProvider implements ServiceProviderInterface
    {
        use ConnectorFinder;

        /**
         * Boot services.
         * 
         * @return void
         */
        public function boot()
        {
            global $config_db;

            if ($config_db['default'] !== null) {
                $this->setConnection();

                // Create a new connector
                $pdo = $this->createConnector();
                $pdo->connect();
            }
        }

        /**
         * {@inheritdoc}
         */
        public function register()
        {
            //
        }

    }
