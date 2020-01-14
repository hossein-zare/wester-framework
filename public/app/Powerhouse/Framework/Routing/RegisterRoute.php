<?php

    namespace Powerhouse\Routing;

    use Powerhouse\Routing\Attributes;
    use Powerhouse\Routing\Traits\MethodParser;
    use Powerhouse\Freeway\NoMethods;
    use Powerhouse\Castles\Storage;

    abstract class RegisterRoute extends Attributes
    {
        use MethodParser;

        /**
         * Register given routes to process.
         *
         * @param  string|array  $methods
         * @param  string  $uri
         * @param  callback|string  $action
         * @return $this
        */
        protected function registerRoute($methods, $uri, $action)
        {
            global $config;

            if (is_string($action) === true && file_exists('./app/Storage/Routes/cache.json') === true && $config['debug'] === false)
                return new NoMethods();

            // If the url catcher is enbaled do not store the route
            if (static::$urlCatcher === true)
                return new NoMethods();

            // Add prefix if there is one
            $prefix = (static::$routingType === 'api') ? 'api/' : '';
            
            // Group config
            $group_prefix = $this->getGroupConfig('prefix');
            $group_middleware = $this->getGroupConfig('middleware');
            $group_services = $this->getGroupConfig('services');
            $group_namespace = $this->getGroupConfig('namespace');
            $group_locale = $this->getGroupConfig('locale');

            // Apply the group prefix
            if ($group_prefix !== null)
                $uri = trim($group_prefix, '/') . '/' . trim($uri, '/');

            // Strip all whitespace in The URI
            $uri = strip_all_whitespace($prefix . trim($uri, '/'), ' ');
            
            // Convert methods to an array
            $methods = $this->parseMethod($methods);
            
            // Check methods
            if (isset(static::$routes[$uri])){
                if ($this->compareMethods($methods, static::$routingType) === false)
                    return new NoMethods();
            }

            // Store the route
            static::$routes[$uri] = [
                'methods' => $methods,
                'action' => $action,
                'wheres' => static::$wheres,
                'namespace' => ($group_namespace !== null) ? $group_namespace : static::$namespace,
                'locale' => null,
                'middleware' => (count($group_middleware) > 0) ? $group_middleware : [],
                'services' => (count($group_services) > 0) ? $group_services : [],
                'parameters' => [],
                'routingType' => static::$routingType
            ];
            
            // Store The URI
            static::$uri = $uri;

            // Apply the group locale
            if ($group_locale !== null)
                $this->locale($locale);
            
            return $this;
        }

        /**
         * Register the url catcher.
         * 
         * @param  callable|string  $action
         * @return void
         */
        protected function registerCatcher($action)
        {
            static::$urlCatcher = true;

            // Store the route
            static::$routes = [];
            static::$routes[''] = [
                'methods' => ['ANY'],
                'action' => $action,
                'wheres' => static::$wheres,
                'namespace' => static::$namespace,
                'locale' => null,
                'middleware' => [],
                'services' => [],
                'parameters' => [],
                'variables' => [],
                'routingType' => 'web'
            ];
            
            // Store The URI
            static::$uri = '';
        }

        /**
         * Cache routes.
         * 
         * @return bool
         */
        protected function cacheRoutes()
        {
            global $config;

            $file = Storage::explore('app')->setDir('Storage')->setDir('Routes');
            $name = 'cache.json';

            if ($file->exists($name) === false || $config['debug'] === true) {
                // Get the routes
                $routes = [];
                foreach (static::$routes as $uri => $data)
                    if (is_object($data['action']) === false)
                        $routes[$uri] = $data;

                // Get the named routes
                $names = static::$namedRoutes;

                // Collect both
                $content = ['routes' => $routes, 'names' => $names];

                $file->put($name, json_encode($content));
            }

            return true;
        }

        /**
         * Load cached routes.
         * 
         * @return bool
         */
        public function loadCache()
        {
            global $config;

            $file = Storage::explore('app')->setDir('Storage')->setDir('Routes');
            $name = 'cache.json';

            if ($file->exists($name) === true && $config['debug'] === false) {
                $content = json_decode($file->get($name, 'file_get_contents'), true);
                
                static::$routes = $content['routes'];
                static::$namedRoutes = $content['names'];

                return true;
            }

            return false;
        }

    }
