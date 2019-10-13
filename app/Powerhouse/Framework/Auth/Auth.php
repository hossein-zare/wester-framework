<?php

	namespace Powerhouse\Auth;

    use App\Transit\Providers\Services\AuthServiceProvider;
    use Models\User;
    use Models\AuthSession;
	use Powerhouse\Support\Str;
	use Powerhouse\Support\Collection;

	class Auth extends AuthServiceProvider
	{

		/**
		 * The user's model.
		 * 
		 * @var \Powerhouse\Support\Collection
		 */
		protected static $user;
        
        /**
         * The authenticated user payload.
         * 
         * @var null
         */
        protected static $payload = null;

		/**
         * Create a new instance of Auth
         * 
         * @return void
         */
        public function __construct()
        {
            $this->verified();
            $this->lastActivity();
        }

		/**
		 * Set up routes.
		 * 
		 * @return bool
		 */
		public function setRoutes($route)
		{
			// Here we should check if the authentication system is active
			if (! $this->isActive())
				return false;
            
			// Now we're going to create a group for the routes
			$routes = array_to_object($this->routes);
			$group = [
				'namespace' => 'Auth',
				'prefix' => $this->prefix
			];

			$route->group($group, function ($sub) use ($routes) {
				// Login
                if (isset($routes->login)) {
                    $sub->get($routes->login, 'Login@form')->name('login');
                    $sub->post($routes->login, 'Login@process');
                }

                // Register
                if (isset($routes->register)) {
                    $sub->get($routes->register, 'Register@form')->name('register');
                    $sub->put($routes->register, 'Register@process');
                }

                // Logout
                if (isset($routes->logout)) {
                    $sub->post($routes->logout, 'Logout@process')->name('logout');
                }

                // Reset password
                if (isset($routes->reset)) {
                    $sub->get($routes->reset, 'Reset@form')->name('resetpassword');
                    $sub->post($routes->reset, 'Reset@process');
                }

                // Change Password
                if (isset($routes->changepassword)) {
                    $sub->get($routes->changepassword, 'ChangePassword@form')->name('resetchangepassword');
                    $sub->patch($routes->changepassword, 'ChangePassword@process');
                }

                // Verify
                if (isset($routes->verify)) {
                    $sub->get($routes->verify, 'Verify@process')->name('verify');
                }
			});
		}

		/**
		 * Determine whether the user is verified.
		 * 
		 * @return bool
		 */
		public function verified()
		{
			if (! $this->isActive())
				return false;

			if ($this->payload() !== false)
				return true;

			if (cookie()->exists($this->cookie)) {
				$payload = $this->getPayload();

				// Check the database 
                $session = new AuthSession();
                $session->select('users.*');
                $session->where([
                    ['auth_sessions.payload', $payload],
                    ['auth_sessions.status', 1]
                ]);
                $session->join('users', 'users.id', '=', 'auth_sessions.user_id')->first();
                $user = $session->get();

                // Store the model data
                if ($user->count() === 1) {
                    $this->setPayload($payload);

                    if ($this->user() === null)
                    	$this->setUser($user);

                    return true;
                }

                cookie()->destroy($this->cookie);
                return false;
            }

            return false;
		}

        /**
         * Log the user out also wipe all of the unauthorized signs.
         * 
         * @return bool
         */
        public function logout()
        {
            if (cookie()->exists($this->cookie)) {
                cookie()->destroy($this->cookie);

                $payload = $this->payload();
                if ($payload === false)
                    return false;

                $session = new AuthSession();
                $session->where("payload", $payload);
                $session->where("status", 1);
                $session->status = 0;
                $session->update();

                $this->setPayload(null);
                return true;
            }

            return false;
        }

        /**
         * Log into the authenticated area.
         * 
         * @param  int  $id
         * @return bool
         */
        public function login(int $id)
        {
            global $config;

            $expiresAt = ($this->expires * 3600) + time();
            $payload = Str::random(30);
            $api_token = Str::random(30);

            AuthSession::create([
                'user_id' => $id,
                'ip_address' => request()->response()->header('REMOTE-ADDR'),
                'user_agent' => request()->response()->header('HTTP-USER-AGENT'),
                'payload' => $payload,
                'api_token' => $api_token,
                'created_at' => time(),
                'last_activity' => time()
            ]);

            // Create an array for the cookie
            $secret = [
                'payload' => $payload,
                'lifetime' => $expiresAt
            ];

            cookie()->create($this->cookie, $secret, $expiresAt);
            
            return $api_token;
        }

        /**
         * Update last activity.
         * 
         * @return void
         */
        public function lastActivity()
        {
            if ($this->verified())
            {
                $payload = $this->payload();

                $session = new AuthSession();
                $session->whereRaw("payload = ? AND status = 1", $payload);
                $session->last_activity = time();
                $session->update();
            }
        }

        /**
         * Determine whether verification is required.
         * 
         * @return bool
         */
        public function verificationRequired()
        {
            return $this->verificationRequired;
        }

		/**
		 * Determine whether the authentication system is active.
		 * 
		 * @return bool
		 */
		public function isActive()
		{
			return isset($this->routes);
		}

		/**
		 * Get the user.
		 * 
		 * @return \Powerhouse\Support\Collection
		 */
		public function user()
		{
			return self::$user;
		}

		/**
		 * Set the user's model.
		 * 
		 * @param  \Powerhouse\Support\Collection  $collection
		 * @return void
		 */
		public function setUser(Collection $collection)
		{
			self::$user = $collection;
		}

        /**
         * Get the remote payload.
         * 
         * @return string
         */
        protected function getPayload()
        {
            $secret = cookie()->get($this->cookie);

            if (is_array($secret) === false || time() > $secret['lifetime']) {
                cookie()->destroy();

                return false;
            }

            return $secret['payload'];
        }

		/**
		 * Get the local payload.
		 * 
		 * @return bool|string
		 */
		protected function payload()
		{
			if (self::$payload !== null)
				return self::$payload;

			return false;
		}

		/**
		 * Set the loal payload
		 * 
		 * @param  string  $payload
		 * @return void
		 */
		protected function setPayload($payload)
		{
			self::$payload = $payload;
		}

		/**
		 * Get the unsigned properties
		 * 
		 * @param  string  $name
		 * @return \Powerhouse\Support\Collection
		 */
		public function __get($name)
		{
			return $this->user()->{$name};
		}

		/**
		 * Get the unsigned methods
		 * 
		 * @param  string  $name
		 * @return \Powerhouse\Support\Collection
		 */
		public function __call($method, $parameters)
		{
			return $this->user()->{$method}($parameters);
		}

	}