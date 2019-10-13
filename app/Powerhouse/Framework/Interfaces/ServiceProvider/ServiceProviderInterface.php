<?php

    namespace Powerhouse\Interfaces\ServiceProvider;

    interface ServiceProviderInterface
    {
        public function boot();
        public function register();
    }
