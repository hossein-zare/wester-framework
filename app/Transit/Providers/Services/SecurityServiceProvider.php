<?php

    namespace App\Transit\Providers\Services;

    use Powerhouse\Interfaces\ServiceProvider\ServiceProviderInterface;

    class SecurityServiceProvider implements ServiceProviderInterface
    {

        /**
         * Boot services.
         * 
         * @return void
         */
        public function boot()
        {
            // Prevent clickjacking
            set_header('X-Frame-Options', "deny");
            set_header('Content-Security-Policy', "frame-ancestors 'none'", false);
        }

        /**
         * {@inheritdoc}
         */
        public function register()
        {
            //
        }

    }
