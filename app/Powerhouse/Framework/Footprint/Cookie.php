<?php

    namespace Powerhouse\Footprint;

    use Exception;
    use App\Transit\Providers\Services\SessionServiceProvider;

    class Cookie
    {

        /**
         * Get the configuration.
         * 
         * @param  string  $name
         * @return mixed
         */
        protected function config($name)
        {
            global $config_cookie;
            return $config_cookie[$name];
        }

        /**
         * Create new sessions.
         *
         * @param  string|array    $name
         * @param  string|null     $value
         * @param  int             $expire
         * @param  string|null     $path
         * @param  string|null     $domain
         * @param  bool            $secure
         * @param  bool            $httponly
         * @return bool
         */
        public function create($name, $value = null, $expire = null, $path = null, $domain = null, $secure = null, $httponly = null)
        {
            global $config, $config_session;

            if ($expire === null)
                $expire = $this->config('expiration');

            if ($domain === null)
                $domain = $this->cookie_domain();

            if ($path === null)
                $path = $config['path'];

            if ($secure === null)
                $secure = $config_session['secure'];

            if ($httponly === null)
                $httponly = $config_session['http_only'];

            if (empty($name) === false) {
                $value = crypto()->encrypt($value);
                setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
                $_COOKIE[$name] = $value;

                return true;
            } else
                throw new Exception("Please sprecify a name for the cookie!");
        }

        /**
         * Get the cookie domain.
         * 
         * @return string|bool
         */
        protected function cookie_domain()
        {
            $session_domain = SessionServiceProvider::$session_domain;
            $bool = is_bool($session_domain);
            return $bool ? ($session_domain ? $_SERVER['SERVER_NAME'] : false) : $session_domain;
        }

        /**
         * Get cookie.
         *
         * @param  string  $name
         * @return null|string
         */
        public function get($name)
        {
            if ($this->has($name)) {
                $value = $_COOKIE[$name];
                return crypto()->decrypt($value);
            }
            
            return null;
        }

        /**
         * Get all cookies.
         *
         * @return array
         */
        public function all()
        {
            return $_COOKIE;
        }
        
        /**
         * Destroy a session.
         *
         * @param  string|null  $name
         * @return string
         */
        public function destroy($name = null)
        {
            global $config;

            if ($name !== null) {
                if ($this->has($name)) {
                    unset($_COOKIE[$name]);
                    setcookie($name, '', time() - 3600, $config['path'], $this->cookie_domain());
                    setcookie($name, '', time() - 3600, '/', $this->cookie_domain());
                } else
                    return false;
            } else {
                if (isset($_SERVER['HTTP_COOKIE'])) {
                    $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
                    foreach ($cookies as $cookie) {
                        unset($_COOKIE[$name]);
                        $parts = explode('=', $cookie);
                        $name = trim($parts[0]);
                        setcookie($name, '', time() - 3600, $config['path'], $this->cookie_domain());
                        setcookie($name, '', time() - 3600, '/', $this->cookie_domain());
                    }
                }
            }

            return true;
        }

        /**
         * Check if the cookie exists.
         *
         * @param  string  $name
         * @return bool
         */
        public function exists($name)
        {
            if (isset($_COOKIE[$name]))
                return true;
            
            return false;
        }

        /**
         * Check if the cookie exists.
         *
         * @param  string  $name
         * @return bool
         */
        public function has($name)
        {
            if (isset($_COOKIE[$name]))
                return true;
            
            return false;
        }

        /**
         * Set a default expiration date for all cookies.
         * 
         * @param  int  $time
         * @return bool
         */
        public static function defaultExpiration($time)
        {
            global $config_cookie;
            if (is_int($time) && (int) $time > 0) {
                $config_cookie['expiration'] = $time;
                return true;
            }

            return false;
        }

    }
