<?php

    namespace App\Transit\Providers\Services;

    use Powerhouse\Interfaces\ServiceProvider\ServiceProviderInterface;
    use Powerhouse\Footprint\Session as ServiceProvider;

    class SessionServiceProvider extends ServiceProvider implements ServiceProviderInterface
    {
        
        /**
         * Session domain configuration.
         * If a boolean TRUE|FALSE is given it turns ON|OFF the process,
         * And a string which is your domain name makes sessions available on it.
         * 
         * @var bool|string
         */
        public static $session_domain = false;

        /**
         * Boot services.
         * 
         * @return void
         */
        public function boot()
        {
            //
        }

        /**
         * {@inheritdoc}
         */
        // public function register()
        //{
            // Session has its own register method.
            // Overwriting the method is illegal and will cause critical issues!
        //}

    }
