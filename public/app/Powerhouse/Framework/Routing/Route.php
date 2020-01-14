<?php

    namespace Powerhouse\Routing;
    
    use Powerhouse\Services\ServiceProvider;
    use Powerhouse\Routing\RegisterRoute;
    use Powerhouse\Castles\URL;
    
    class Route extends RegisterRoute
    {

        /**
         * Routes will be saved in this array.
         *
         * @var array
         */
        public static $routes = [];

        /**
         * Named routes.
         *
         * @var array
         */
        public static $namedRoutes = [];
        
        /**
         * The HTTP URI of the current route.
         *
         * @var string
         */
        public static $uri;
        
        /**
         * The request uri.
         *
         * @var string
         */
        protected static $request_uri;
        
        /**
         * Routing type.
         *
         * @var string
         */
        protected static $routingType = 'web';
        
        /**
         * The type of the action.
         *
         * @var string
         */
        protected static $actionType = 'function';

        /**
         * Group config used for groups.
         * 
         * @var array
         */
        protected static $groupConfig = [
            'prefix' => null,
            'middleware' => [],
            'services' => [],
            'namespace' => null,
            'locale' => null
        ];
        
        /**
         * Url catcher status.
         * 
         * @var bool
         */
        protected static $urlCatcher = false;

        /**
         * Create a new instance.
         *
         * @return void
         */
        public function __construct()
        {
            //
        }

        /**
         * Get the current route.
         * 
         * @return string
         */
        public static function currentRoute()
        {
            return self::$request_uri;
        }

        /**
         * Get the routing method "web", "api".
         * 
         * @return string
         */
        public static function routingType()
        {
            if (in_array(self::currentRoute(), self::$routes) === false)
                return null;

            return static::$routingType;
        }

        /**
         * Get the name the current route.
         * 
         * @return string
         */
        public static function currentRouteName()
        {
            $uri = self::$uri;
            $name = null;

            array_walk(self::$namedRoutes, function ($a, $b) use ($uri, &$name){
                if ($a === $uri)
                    $name = $b;
            });

            return $name;
        }

        /**
         * Get the routing type.
         * 
         * @return string
         */
        public static function getRoutingType()
        {
            return URL::routingType();
        }

        /**
         * Determine whether the current route is equal to the given value.
         * 
         * @param  string  $name
         * @return bool
         */
        public static function named($name)
        {
            $route = self::currentRouteName();
            if ($route === $name)
                return true;
            return false;
        }
        
        /**
         * Register a new GET method.
         *
         * @param  string  $uri
         * @param  callback|string  $action
         * @return $this
         */
        public function get($uri, $action)
        {
            return $this->registerRoute(['GET', 'HEAD'], $uri, $action);
        }
        
        /**
         * Register a new POST method.
         *
         * @param  string  $uri
         * @param  callback|string  $action
         * @return  $this
         */
        public function post($uri, $action)
        {
            return $this->registerRoute('POST', $uri, $action);
        }
        
        /**
         * Register a new PUT method.
         *
         * @param  string  $uri
         * @param  callback|string  $action
         * @return $this
         */
        public function put($uri, $action)
        {
            return $this->registerRoute('PUT', $uri, $action);
        }
        
        /**
         * Register a new PATCH method.
         *
         * @param  string  $uri
         * @param  callback|string  $action
         * @return $this
         */
        public function patch($uri, $action)
        {
            return $this->registerRoute('PATCH', $uri, $action);
        }
        
        /**
         * Register a new DELETE method.
         *
         * @param  string  $uri
         * @param  callback|string  $action
         * @return $this
         */
        public function delete($uri, $action)
        {
            return $this->registerRoute('DELETE', $uri, $action);
        }
        
        /**
         * Register a new HEAD method.
         *
         * @param  string  $uri
         * @param  callback|string  $action
         * @return $this
         */
        public function head($uri, $action)
        {
            return $this->registerRoute('HEAD', $uri, $action);
        }

        /**
         * Register a new OPTIONS method.
         *
         * @param  string  $uri
         * @param  callback|string  $action
         * @return $this
         */
        public function options($uri, $action)
        {
            return $this->registerRoute('OPTIONS', $uri, $action);
        }
        
        /**
         * Register ANY method responding to any HTTP Method.
         *
         * @param  string  $uri
         * @param  callback|string  $action
         * @return $this
         */
        public function any($uri, $action)
        {
            return $this->registerRoute($this->verbs, $uri, $action);
        }
        
        /**
         * Register a MANY method.
         *
         * @param  string  $methods
         * @param  string  $uri
         * @param  callback|string  $action
         * @return $this
         */
        public function many($methods, $uri, $action)
        {
            return $this->registerRoute($methods, $uri, $action);
        }

        /**
         * Catch every route and show the given page.
         * 
         * @param  callback|string  $action
         * @return $this
         */
        public function catchAll($action)
        {
            return $this->registerCatcher($action);
        }
        
        /**
         * Set the routing type.
         *
         * @param  string  $name
         * @return void
         */
        public function setType($name)
        {
            static::$routingType = $name;
        }

        /**
         * Enable the web routing.
         * 
         * @param  string  $name
         * @return void
         */
        public function seek(string $name)
        {
            $this->setType($name);
        }

        /**
         * Set up group config.
         * 
         * @param  string  $name
         * @param  string|array  $value
         * @return void
         */
        public function setGroupConfig($name, $value)
        {
            if ($name === 'middleware')
                $value = toArray($value);

            static::$groupConfig[$name] = $value;
        }

        /**
         * Get the group config.
         * 
         * @param  string  $name
         * @return mixed
         */
        public function getGroupConfig($name)
        {
            return static::$groupConfig[$name];
        }

        /**
         * Reset the group config.
         * 
         * @param  string  $name
         * @return void
         */
        public function resetGroupConfig($name)
        {
            if ($name === 'prefix')
                static::$groupConfig[$name] = null;

            if ($name === 'middleware')
                static::$groupConfig[$name] = [];

            if ($name === 'namespace')
                static::$groupConfig[$name] = null;
        }
    }
