<?php

    namespace App\Transit\Providers\Services;

    use Powerhouse\Interfaces\ServiceProvider\ServiceProviderInterface;
    use Powerhouse\Support\Extendable\Spark;
    use Powerhouse\Support\Extendable\Validator;
    use Powerhouse\Http\Request;

    class AppServiceProvider implements ServiceProviderInterface
    {
        
        /**
         * Boot services.
         * 
         * @return void
         */
        public function boot()
        {
            Request::macro('introduce', function ($name) {
                echo 'Hello ' . $name .' !';
            });
        }

        /**
         * {@inheritdoc}
         */
        public function register()
        {
            //
        }

    }
