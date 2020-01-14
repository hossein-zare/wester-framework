<?php

    namespace Powerhouse\Routing;

    use Powerhouse\Routing\Traits\ParameterParser;
    use Powerhouse\Routing\Traits\MiddlewareParser;
    use Powerhouse\Routing\Traits\ServiceProviderParser;
    use Powerhouse\Localization\App;
    use Powerhouse\Castles\Response;
    use jsonSerializable;

    class Driver extends ParseUri
    {
        use ParameterParser,
            MiddlewareParser,
            ServiceProviderParser;

        /**
         * Parse the registered routes.
         *
         * @var array
         */
        public function parseRoutes()
        {
            $this->cacheRoutes();

            $request_uri = $this->prepareUrl($_SERVER['REQUEST_URI']);

            foreach (static::$routes as $uri => $data) {
                static::$uri = $uri;
                static::$routingType = $data['routingType'];

                if ($this->compareMethods($data['methods'], $data['routingType']) === false)
                    continue;

                if ($this->parseUri($uri, $request_uri) === false)
                    if (static::$urlCatcher === false)
                        continue;

                if ($this->parseMiddleware($data['middleware']) === false)
                    break;

                $this->parseServices($data['services']);

                if ($data['locale'] !== null)
                    App::setLocale($data['locale']);

                // Execute
                $this->executeRoute($uri, $data);

                RouterStatus::increase();
                break;
            }

            if (RouterStatus::get() === 0) {
                static::$uri = null;
                abort(404);

                return;
            }
        }

        /**
         * Execute the route.
         * 
         * @param  string  $uri
         * @param  array  $data
         * @return void
         */
        protected function executeRoute(string $uri, array $data)
        {
            $result = $this->callback(static::$routes[$uri]);
            switch (static::$actionType) {
                case 'function':

                    if ($data['routingType'] === 'api')
                        $this->apiPort($result);

                    elseif ($data['routingType'] === 'web')
                        $this->regularPort($result);

                break;
                case 'view':
                    echo spark()->getRendered();
                break;
            }
        }

        /**
         * Execute the result of an api route.
         * 
         * @param  mixed  $array
         * @return void
         */
        protected function apiPort($result)
        {
            if ($result !== null && !empty($result)) {
                Response::contentType('json');
                echo json_message($result);
            } else
                echo json_message([]);
        }

        /**
         * Execute the result of a web route.
         * 
         * @param  mixed  $array
         * @return void
         */
        protected function regularPort($result)
        {
            if (is_array($result) === true || $result instanceof jsonSerializable) {
                Response::contentType('json');
                echo json_message($result);
            } else {
                echo $result;
            }
        }

        /**
         * Compare methods of the routes.
         *
         * @param  array  $methods
         * @param  array  $routingType
         * @return bool
         */
        protected function compareMethods($methods, $routingType)
        {
            if (in_array('ANY', $methods))
                return true;

            $request_method = request()->method($routingType);

            return in_array($request_method, $methods);
        }
        
    }
