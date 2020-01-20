<?php

    namespace Powerhouse\Http;

    use Powerhouse\Routing\Route;
    use Powerhouse\Support\Str;
    use Exception;

    class URL
    {

        /**
         * Get the path.
         * 
         * @return string
         */
        public function configPath()
        {
            global $config;
            $path = trim($config['path'], "/");

            if ($path === '')
                return $path;

            $path = '/' . $path;
            return $path;
        }

        /**
         * Path binder.
         * 
         * @param  string  $a
         * @param  string  $b
         * @return string
         */
        public function pathBinder($a, $b)
        {
            if ($b === '' || $b === null)
                return $a;

            $b = ltrim($b, '/');
            return implode('/', [$a, $b]);
        }

        /**
         * Get the current path.
         * 
         * @param  string  $path
         * @return string
         */
        public function path($path = '')
        {
            $configPath = $this->configPath(); //

            return $this->pathBinder($configPath, $path);
        }

        /**
         * Get the Host address.
         * 
         * @param  string  $uri
         * @return string
         */
        public function host($uri)
        {
            $url = sprintf(
                "%s://%s",
                isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http',
                strtok($_SERVER['HTTP_HOST'], ':')
            );

            if (! in_array($_SERVER['SERVER_PORT'], [80, 443]))
                $url.= ':' . $_SERVER['SERVER_PORT'];

            if ($uri !== '')
                $uri = '/' . trim($uri, '/');

            return $url . $uri;
        }

        /**
         * Get the url path.
         * 
         * @param  bool  $query
         * @return string
         */
        public function urlPath($query = false)
        {
            $uri = $_SERVER['REQUEST_URI'];
            
            if ($query === false)
                $uri = strtok($uri, '?');

            return rtrim($uri, '/');
        }

        /**
         * Get the full URL.
         * 
         * @param  bool  $query
         * @return string
         */
        public function full($query = false)
        {
            $uri = $_SERVER['REQUEST_URI'];

            if ($query === false)
                $uri = strtok($uri, '?');

            $port = '';
            if (! in_array($_SERVER['SERVER_PORT'], [80, 443]))
                $port = ':' . $_SERVER['SERVER_PORT'];

            $url = sprintf(
                "%s://%s%s%s",
                isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http',
                strtok($_SERVER['HTTP_HOST'], ':'),
                $port,
                $uri
            );

            return rtrim($url, '/');
        }

        /**
         * Get the named routes.
         * 
         * @param  string  $name
         * @param  array  $parameters
         * @return string
         */
        public function route($name, $parameters = [])
        {
            $routes = Route::$routes;
            $namedRoutes = Route::$namedRoutes;

            if (isset($namedRoutes[$name]) === true) {
                $route = $namedRoutes[$name];

                $route = preg_replace_callback('/{([a-z]+)\??}/', function ($matches) use ($parameters) {
                    if (isset($parameters[$matches[1]]))
                        return $parameters[$matches[1]];

                    return '';
                }, $route);

            } else
                throw new Exception("The given route <b>'{$name}'</b> doesn't exist!");

            return $this->host($this->path($route));
        }

        /**
         * Get the named routes (Relative).
         * 
         * @param  string  $name
         * @param  array  $parameters
         * @return string
         */
        public function relativeRoute($name, $parameters = [])
        {
            $routes = Route::$routes;
            $namedRoutes = Route::$namedRoutes;

            if (isset($namedRoutes[$name]) === true) {
                $route = $namedRoutes[$name];

                $route = preg_replace_callback('/{([a-z]+)\??}/', function ($matches) use ($parameters) {
                    if (isset($parameters[$matches[1]]))
                        return $parameters[$matches[1]];

                    return '';
                }, $route);

            } else
                throw new Exception("The given <b>'{$name}'</b> route doesn't exist!");

            return $this->path('/' . $route);
        }

        /**
         * Get the routing method "web", "api".
         * 
         * @return string
         */
        public function routingType()
        {
            $value = trim($this->path('api'), '/');
            $url = trim($this->urlPath(), '/');

            if ($url === $value || strpos($url, $value . '/') === 0)
                return 'api';
            return 'web';
        }
        
        /**
         * Append query string to the local address.
         * 
         * @param  string  $query
         * @param  bool  $renew
         * @return string
         */
        public function localQuery($query, $renew = false)
        {
            $url = $this->urlPath();
            return $this->appendQueryStringToURL($url, $query);
        }

        /**
         * Append query string to the remote address.
         * 
         * @param  string  $query
         * @param  bool  $renew
         * @return string
         */
        public function remoteQuery($url, $query, $renew = false)
        {
            return $this->appendQueryStringToURL($url, $query);
        }

        /**
         * Append query string to URL.
         * 
         * @param  string  $url
         * @param  string|array  $query
         * @return string
         */
        public function appendQueryStringToURL(string $url, $query)
        {
            // The query is empty, return the original url straightaway
            if (empty($query))
                return $url;

            $parsedUrl = parse_url($url);
            if (empty($parsedUrl['path']))
                $url .= '/';

            // If the query is array convert it to string
            $queryString = is_array($query) ? http_build_query($query) : $query;

            // Check if there is already any query string in the URL
            if (empty($parsedUrl['query'])) {
                // Remove duplications
                parse_str($queryString, $queryStringArray);
                $url = rtrim($url, '?');
                $url .= '?' . http_build_query($queryStringArray);
            } else {
                $queryString = $parsedUrl['query'] . '&' . $queryString;

                // Remove duplications
                parse_str($queryString, $queryStringArray);

                // Place the updated query in the original query position
                $url = substr_replace($url, http_build_query($queryStringArray), strpos($url, $parsedUrl['query']), Str::length($parsedUrl['query']));
            }

            return $url;
        }

    }
