<?php

    namespace App\Transit\Providers\Services;

    use Powerhouse\Interfaces\ServiceProvider\ServiceProviderInterface;
    use Powerhouse\Castles\View;

    class ViewServiceProvider implements ServiceProviderInterface
    {

        /**
         * Boot services.
         * 
         * @return void
         */
        public function boot()
        {
            View::composer("pages/*", function ($view) {
                return $view->with('admin', 'hossein');
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
