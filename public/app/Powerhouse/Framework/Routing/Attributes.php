<?php

    namespace Powerhouse\Routing;
    
    abstract class Attributes extends Driver
    {

        /**
         * General regular expressions supposed to be applied on every route.
         *
         * @var array
         */
        protected static $wheres = [];
        
        /**
         * General namespace supposed be applied on every route.
         *
         * @var array
         */
        protected static $namespace = null;
    
        /**
         * Set a regular expression requirement on the route.
         *
         * @param  array|string  $identifier
         * @param  null|string  $expression
         * @return $this
         */
        public function where($identifier, $expression = null)
        {
            $uri = static::$uri;
            if (is_array($identifier)) {
                static::$routes[$uri]['wheres'] = array_merge(static::$routes[$uri]['wheres'], $identifier);
            } else if ($expression !== null){
                static::$routes[$uri]['wheres'] = array_merge(static::$routes[$uri]['wheres'], [$identifier => $expression]);
            }
            
            return $this;
        }
        
        /**
         * Set a general regular expression requirement on the route.
         *
         * @param  array|string  $identifier
         * @param  null|string  $expression
         * @return void
         */
        public static function pattern($identifier, $expression = null)
        {
            if (is_array($identifier)) {
                self::$wheres = array_merge(self::$wheres, $identifier);
            } else if ($expression !== null){
                self::$wheres = array_merge(self::$wheres, [$identifier => $expression]);
            }
        }
        
        /**
         * Set a namespace on the route controller.
         *
         * @param  string  $namespace
         * @return $this
         */
        public function namespace($namespace)
        {
            static::$routes[static::$uri]['namespace'] = $namespace;
            return $this;
        }
        
        /**
         * Set a namespace on the route controller in a static method.
         *
         * @param  string  $namespace
         * @return $this
         */
        public static function appNamespace($namespace)
        {
            self::$namespace = $namespace;
        }
        
        /**
         * Set middleware on the route.
         *
         * @param  string|array  $middleware
         * @return $this
         */
        public function middleware($middleware)
        {
            static::$routes[static::$uri]['middleware'] = toArray($middleware);
            return $this;
        }

        /**
         * Add more middleware on the route.
         *
         * @param  string|array  $middleware
         * @return $this
         */
        public function addMiddleware($middleware)
        {
            $old = static::$routes[static::$uri]['middleware'];
            static::$routes[static::$uri]['middleware'] = array_merge($old, toArray($middleware));

            return $this;
        }

        /**
         * Set services on the route.
         *
         * @param  string|array  $services
         * @return $this
         */
        public function service($services)
        {
            static::$routes[static::$uri]['services'] = toArray($services);
            return $this;
        }

        /**
         * Add more services on the route.
         *
         * @param  string|array  $services
         * @return $this
         */
        public function addService($services)
        {
            $old = static::$routes[static::$uri]['services'];
            static::$routes[static::$uri]['services'] = array_merge($old, toArray($sevices));
            return $this;
        }

        /**
         * Routing group.
         * 
         * @param  array  $items
         * @param  callback  $callback
         * @return void
         */
        public function group(array $items, $callback)
        {
            foreach ($items as $item => $value) {
                $this->setGroupConfig($item, $value);
            }

            $callback($this);

            $this->resetGroupConfig('prefix');
            $this->resetGroupConfig('middleware');
            $this->resetGroupConfig('services');
            $this->resetGroupConfig('namespace');
            $this->resetGroupConfig('locale');
        }
        
        /**
         * Set a locale on the route.
         *
         * @param  string  $locale
         * @return $this
         */
        public function locale($locale)
        {
            static::$routes[static::$uri]['locale'] = $locale;
            return $this;
        }

        /**
         * Name the route.
         *
         * @param  string  $name
         * @return $this
         */
        public function name($name)
        {
            if (! isset(static::$namedRoutes[$name]))
                static::$namedRoutes[$name] = static::$uri;

            return $this;
        }
        
        /**
         * Set variables on the route.
         *
         * @return $this
         */
        public function with()
        {
            $num = func_num_args();
            $arguments = func_get_args();

            if ($num === 1 && is_array($arguments[0]))
                spark()->setArguments($arguments[0]);
            elseif ($num === 2 && is_string($arguments[0]) && $arguments[1])
                spark()->setArguments([$arguments[0] => $arguments[1]]);
            
            return $this;
        }

    }
