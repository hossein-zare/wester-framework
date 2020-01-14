<?php

    namespace App\Transit\Providers\Services;

    use Powerhouse\Interfaces\ServiceProvider\ServiceProviderInterface;

    class AuthServiceProvider implements ServiceProviderInterface
    {

        /**
         * The name of the authentication cookie
         * 
         * @var string
         */
        protected $cookie = 'auth';

        /**
         * Authentication prefix
         * Leave it null if not in use.
         * 
         * @var string|null
         */
        protected $prefix = null;

        /**
         * Authentication routes.
         * 
         * @var array
         */
        protected $routes = [
            'login' => 'login',
            'register' => 'register',
            'reset' => 'password/reset',
            'changepassword' => 'password/reset/{serial}',
            'verify' => 'verify/{serial}',
            'logout' => 'logout'
        ];

        /**
         * Verification required.
         * 
         * @var bool
         */
        public $verificationRequired = false;

        /**
         * Authentication expires at (in hours).
         * 
         * @var int
         */
        protected $expires = 24;

        /**
         * The home page for logged users.
         * 
         * @return string
         */
        public function home()
        {
            return path('/');
        }

        /**
         * The destination when the user is logged out.
         * 
         * @return string
         */
        public function fallback()
        {
            return path('/login');
        }

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
