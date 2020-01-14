<?php

    namespace Powerhouse\Footprint;
    
    use App\Transit\Providers\RouteServiceProvider;
    use App\Transit\Http\Handler\HttpConfiguration;
    use Powerhouse\Support\Str;
    
    class Session extends HttpConfiguration
    {
        /**
         * Session domain configuration.
         * 
         * @var bool|string
         */
        public static $session_domain = true;

        /**
         * Indicates whether the session was destroyed.
         * 
         * @var bool
         */
        protected static $destroyed = false;

        /**
         * Configures the sessions.
         *
         * @return $this
         */
        public function register()
        {
            global $config_session;

            // Wester session name
            session_name('wester_session');
            
            // Specify the domain name
            if (static::$session_domain !== false) {
                $params = session_get_cookie_params();
                session_set_cookie_params( 
                    $config_session['lifetime'],
                    $params["path"], 
                    is_string(static::$session_domain) ? static::$session_domain : $_SERVER['SERVER_NAME'], 
                    $config_session['secure'], 
                    $config_session['http_only']
                );
            }

            // Start session
            session_start();
            
            // Create the csrf token
            if (!isset($_SESSION['_token']))
                $_SESSION['_token'] = Str::random(30);
            
            return $this;
        }
        
        /**
         * Create new sessions.
         *
         * @param  string|array  $name
         * @param  string|null  $value
         * @return bool
         */
        public function create($name, $value = null)
        {
            if (is_array($name)) {
                foreach ($name as $key => $value)
                    if ($this->has($key) === false) {
                        $value = crypto()->encrypt($value);
                        $_SESSION[$key] = $value;

                        return true;
                    } else
                        return false;
            }
            
            if ($this->has($name) === false) {
                $value = crypto()->encrypt($value);
                $_SESSION[$name] = $value;

                return true;
            }
            
            return false;
        }
        
        /**
         * Set a new value to the session.
         *
         * @param  string|array  $name
         * @param  string|null  $value
         * @return bool
         */
        public function put($name, $value = null)
        {
            if (is_array($name)) {
                foreach ($name as $key => $value)
                    if ($this->has($key) === true) {
                        $value = crypto()->encrypt($value);
                        $_SESSION[$key] = $value;
                        return true;
                    } else
                        return false;
            }
            
            if ($this->has($name) === true) {
                $value = crypto()->encrypt($value);
                $_SESSION[$name] = $value;

                return true;
            }
            
            return false;
        }
        
        /**
         * Get session.
         *
         * @param  string  $name
         * @return null|string
         */
        public function get($name)
        {
            if ($this->has($name)) {
                if ($name !== '_token')
                    return crypto()->decrypt($_SESSION[$name]);

                return $_SESSION[$name];
            }
            
            return null;
        }
        
        /**
         * Get all sessions.
         *
         * @return array
         */
        public function all()
        {
            return $_SESSION;
        }

        /**
         * Flash messages.
         * 
         * @param  string  $name
         * @param  mixed  $value
         * @return bool
         */
        public function flash(string $name, $value = null)
        {
            $json = $this->get('flash');
            $toArray = ($json !== null) ? json_decode($json, true) : [];

            // Delete exceptions from the inputs
            $value = $this->deleteExceptions($name, $value);

            if (isset($toArray[$name]) === false)
                $toArray[$name] = $value;
            else {
                if (is_array($toArray[$name]) === true)
                    $toArray[$name] = array_merge($toArray[$name], $value);
                else
                    $toArray[$name] = $value;
            }

            if ($this->has('flash'))
                return $this->put('flash', json_encode($toArray));

            return $this->create('flash', json_encode($toArray));
        }

        /**
         * Get the flashed messages.
         * 
         * @param  string  $name
         * @return  array|null
         */
        public function flashed(string $name)
        {
            $json = $this->get('flash');
            $toArray = ($json !== null) ? json_decode($json, true) : null;

            return isset($toArray[$name]) ? $toArray[$name] : null;
        }

        /**
         * Delete exceptions from inputs.
         * 
         * @param  string  $name
         * @param  array  $value
         * @return array
         */
        protected function deleteExceptions($name, $value)
        {
            if ($name === 'errors')
                return $value;
            
            $internal = ['_token', '_method'];

            if (is_array($value) === false)
                return $value;

            // External exceptions
            foreach ($this->dontFlash as $except)
                if (isset($value[$except]) === true)
                    unset($value[$except]);

            // Internal exceptions
            foreach ($internal as $except)
                if (isset($value[$except]) === true)
                    unset($value[$except]);

            return $value;
        }
        
        /**
         * Destroy a session.
         *
         * @param  string|null  $name
         * @return string
         */
        public function destroy($name = null)
        {
            if ($name !== null) {
                if ($this->has($name))
                    unset($_SESSION[$name]);
                else
                    return false;
            } else {
                $this->regenerate(true);
                self::$destroyed = true;
                session_unset();
                session_destroy();
                session_write_close();
            }
            
            return true;
        }
        
        /**
         * Regenerate the session id.
         *
         * @param  bool  $old
         * @return bool
         */
        public function regenerate($old = false)
        {
            if (self::$destroyed === false)
                if (request()->response()->data()->status === true)
                    return session_regenerate_id($old);
            
            return false;
        }
        
        /**
         * Check if the session exists.
         *
         * @param  string  $name
         * @return bool
         */
        public function exists($name)
        {
            if (isset($_SESSION[$name]))
                return true;
            
            return false;
        }
        
        /**
         * Check if the session exists.
         *
         * @param  string  $name
         * @return bool
         */
        public function has($name)
        {
            if (isset($_SESSION[$name]))
                return true;
            
            return false;
        }

    }
