<?php

namespace Powerhouse\Routing;

use Exception;
use Powerhouse\Helpers\ArrayHelper;

class Mapper
{

    /**
     * The registered routes.
     * 
     * @var array
     */
    protected static $routes = [];

    /**
     * The named routes.
     * 
     * @var array
     */
    protected static $namedRoutes = [];

    /**
     * Group configuration.
     * 
     * @var array
     */
    protected $groupConfiguration = [];

    /**
     * Create a mapper instance.
     * 
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Register a GET route.
     * 
     * @param  string  $uri
     * @param  callback|string  $func
     * @return Powerhouse\Routing\Mapper
     */
    public function get(string $uri, $func)
    {
        return $this->registerRoute('GET', $uri, $func);
    }

    /**
     * Register a POST route.
     * 
     * @param  string  $uri
     * @param  callback|string  $func
     * @return Powerhouse\Routing\Mapper
     */
    public function post(string $uri, $func)
    {
        return $this->registerRoute('POST', $uri, $func);
    }

    /**
     * Register a DELETE route.
     * 
     * @param  string  $uri
     * @param  callback|string  $func
     * @return Powerhouse\Routing\Mapper
     */
    public function delete(string $uri, $func)
    {
        return $this->registerRoute('DELETE', $uri, $func);
    }

    /**
     * Register a HEAD route.
     * 
     * @param  string  $uri
     * @param  callback|string  $func
     * @return Powerhouse\Routing\Mapper
     */
    public function head(string $uri, $func)
    {
        return $this->registerRoute('HEAD', $uri, $func);
    }

    /**
     * Register a OPTIONS route.
     * 
     * @param  string  $uri
     * @param  callback|string  $func
     * @return Powerhouse\Routing\Mapper
     */
    public function options(string $uri, $func)
    {
        return $this->registerRoute('OPTIONS', $uri, $func);
    }

    /**
     * Register a PUT route.
     * 
     * @param  string  $uri
     * @param  callback|string  $func
     * @return Powerhouse\Routing\Mapper
     */
    public function put(string $uri, $func)
    {
        return $this->registerRoute('PUT', $uri, $func);
    }

    /**
     * Register a PATCH route.
     * 
     * @param  string  $uri
     * @param  callback|string  $func
     * @return Powerhouse\Routing\Mapper
     */
    public function patch(string $uri, $func)
    {
        return $this->registerRoute('PATCH', $uri, $func);
    }

    /**
     * Register a View route.
     * 
     * @param  string  $uri
     * @param  string  $name
     * @return Powerhouse\Routing\Mapper
     */
    public function view(string $uri, string $name)
    {
        return $this->registerRoute('GET', $uri, "view:{$name}");
    }

    /**
     * Register a route.
     * 
     * @param  string  $method
     * @param  string  $uri
     * @param  callback|string  $func
     * @return Powerhouse\Routing\Mapper
     */
    public function registerRoute(string $method, string $uri, $func)
    {
        $data = (object) $this->routeConfiguration();

        self::$routes[] = [
            'uri' => $this->normalizeUri($data->uri, $data->index, $uri),
            'method' => strtoupper($method),
            'func' => $func,
            'locale' => $data->locale,
            'context' => $data->context,
            'name' => null,
            'namespace' => $data->namespace,
            'middleware' => $data->middleware,
            'pattern' => $data->pattern
        ];

        return $this;
    }

    /**
     * Normalize the uri
     * 
     * @param  string  $suffix
     * @param  string  $uri
     * @return string
     */
    protected function normalizeUri($suffix, $index, $uri)
    {
        $uri = trim($uri, '/');
        return isset($this->groupConfiguration[$index]['uri']) ?
                    $suffix . '/' . $uri : $uri;
    }

    /**
     * Set locale to the route.
     * 
     * @param  string  $locale
     * @return Powerhouse\Routing\Mapper
     */
    public function locale(string $locale)
    {
        self::$routes[ArrayHelper::getLastIndex(self::$routes)]['locale'] = $locale;

        return $this;
    }

    /**
     * Add context to the route.
     * 
     * @param  string|array  $key
     * @param  string|null  $value
     * @return Powerhouse\Routing\Mapper
     */
    public function context($key, $value = null)
    {
        $context = $value ? [$key => $value] : $key;
        return $this->assignContext($context);
    }

    /**
     * Wipe the context of the route.
     * 
     * @return Powerhouse\Routing\Mapper
     */
    public function wipeContext()
    {
        return $this->assignContext([]);
    }

    /**
     * Assign context.
     * 
     * @param  string  $context
     * @return Powerhouse\Routing\Mapper
     */
    public function assignContext(array $context)
    {
        self::$routes[ArrayHelper::getLastIndex(self::$routes)]['context'] = $context;

        return $this;
    }

    /**
     * Set name to the route.
     * 
     * @param  string  $name
     * @return Powerhouse\Routing\Mapper
     */
    public function name(string $name)
    {
        return $this->assignNamedRoute($name);
    }

    /**
     * Set namespace to the route.
     * 
     * @param  string  $locale
     * @return Powerhouse\Routing\Mapper
     */
    public function namespace(string $namespace)
    {
        self::$routes[ArrayHelper::getLastIndex(self::$routes)]['namespace'] = $namespace;

        return $this;
    }

    /**
     * Set pattern to the route.
     * 
     * @param  array  $group
     * @return Powerhouse\Routing\Mapper
     */
    public function pattern(array $group)
    {
        self::$routes[ArrayHelper::getLastIndex(self::$routes)]['pattern'] = $group;

        return $this;
    }

    /**
     * Add a new group of patterns to the route.
     * 
     * @param  array  $group
     * @return Powerhouse\Routing\Mapper
     */
    public function addPattern(array $group)
    {
        $pattern = self::$routes[ArrayHelper::getLastIndex(self::$routes)]['pattern'];
        self::$routes[ArrayHelper::getLastIndex(self::$routes)]['pattern'] = array_merge($pattern, $group);

        return $this;
    }

    /**
     * Set middle to the route.
     * 
     * @param  array  $group
     * @return Powerhouse\Routing\Mapper
     */
    public function middleware(array $group)
    {
        self::$routes[ArrayHelper::getLastIndex(self::$routes)]['middleware'] = $group;

        return $this;
    }

    /**
     * Add new middleware.
     * 
     * @param  array  $group
     * @return Powerhouse\Routing\Mapper
     */
    public function addMiddleware(array $group)
    {
        $middleware = self::$routes[ArrayHelper::getLastIndex(self::$routes)]['middleware'];
        self::$routes[ArrayHelper::getLastIndex(self::$routes)]['middleware'] = array_merge($middleware, $group);

        return $this;
    }

    /**
     * Assign the route as a named route.
     * 
     * @param  string  $name
     * @return Powerhouse\Routing\Mapper
     */
    protected function assignNamedRoute(string $name)
    {
        if (isset(self::$namedRoutes[$name]))
            throw new Exception("A route has already been assigned with `{$name}` name.");

        $index = ArrayHelper::getLastIndex(self::$routes);
        self::$namedRoutes[$name] = $index;
        self::$routes[$index]['name'] = $name;

        return $this;
    }

    /**
     * Create a group.
     * 
     * @param  array  $config
     * @param  callback  $func
     * @return Powerhouse\Routing\Mapper
     */
    public function group(array $config, callable $func)
    {
        $this->groupConfiguration[] = $config;
        $func($this);
        array_pop($this->groupConfiguration);
    }

    /**
     * Get the configuration of the current route.
     * 
     * @return array
     */
    protected function routeConfiguration()
    {
        // returns null when there's no element in the array.
        $index = (int) ArrayHelper::getLastIndex($this->groupConfiguration);
        $data = [
            'index' => $index,
            'uri' => null,
            'locale' => null,
            'namespace' => null,
            'middleware' => [],
            'pattern' => [],
            'context' => []
        ];

        foreach ($this->groupConfiguration as $key => $value) {
            // Uri
            if (isset($value['uri']))
                $data['uri'].= '/' . $value['uri'];

            // Locale
            if (isset($value['locale']))
                $data['locale'] = $value['locale'];

            // Namespace
            if (isset($value['namespace']) && $value['namespace'] !== null)
                $data['namespace'].= '/' . $value['namespace'];

            // Middleware
            if (isset($value['middleware']))
                $data['middleware'] = array_merge($data['middleware'], $value['middleware']);

            // Pattern
            if (isset($value['pattern']))
                $data['pattern'] = array_merge($data['pattern'], $value['pattern']);

            // Context
            if (! isset($this->groupConfiguration[$index]['wipeContext']) || $this->groupConfiguration[$index]['wipeContext'] !== true)
                if (isset($value['context']))
                    $data['context'] = array_merge($data['context'], $value['context']);
        }

        // Normalize data
        $data['uri'] = trim($data['uri'], '/');
        $data['namespace'] = trim($data['namespace'], '/');

        return $data;
    }

    public function serve()
    {
        // self::$routes[0]['func']();
        var_dump(self::$routes);
    }

}
