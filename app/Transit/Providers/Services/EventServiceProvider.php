<?php

    namespace App\Transit\Providers\Services;

    use Powerhouse\Interfaces\ServiceProvider\ServiceProviderInterface;

    class EventServiceProvider implements ServiceProviderInterface
    {
        
        /**
         * The event listener mappings for the application.
         *
         * @var array
         */
        protected $listen = [
            \Powerhouse\Foundation\Auth\Events\Registered::class => [
                \Powerhouse\Foundation\Auth\Listeners\SendVerificationMail::class,
            ],
            \Powerhouse\Foundation\Auth\Events\NewPasswordVerification::class => [
                \Powerhouse\Foundation\Auth\Listeners\SendPasswordVerificationMail::class,
            ]
        ];

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
        public function register()
        {
            //
        }

    }
