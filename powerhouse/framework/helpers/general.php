<?php

use Fountain\Repository\Repository;
use Fountain\Loader\Config;
use Powerhouse\Routing\Router;
use Powerhouse\Gate\Http;
use Powerhouse\Gate\Http\Request;
use Powerhouse\Gate\Http\Response;

if (! function_exists('toObject')) {
    /**
     * Convert value into an object.
     * 
     * @param  mixed  $data
     * @return object
     */
    function toObject($value)
    {
        if (! is_array($value))
            return $value;

        return json_decode(json_encode($value));
    }
}

if (! function_exists('config')) {
    /**
     * Get the configuration.
     * 
     * @param  string  $section
     * @return Fountain\Loader\Config
     */
    function config($section = null)
    {
        if ($section !== null)
            return (Repository::get(Config::class))->$section();
        return Repository::get(Config::class);
    }
}

if (! function_exists('router')) {
    /**
     * Create a router instance.
     * 
     * @return Powerhouse\Routing\Router
     */
    function router()
    {
        return Repository::get(Router::class);
    }
}

if (! function_exists('http')) {
    /**
     * Create a http instance.
     * 
     * @return Powerhouse\Gate\Http
     */
    function http()
    {
        return Repository::get(Http::class);
    }
}

if (! function_exists('request')) {
    /**
     * Create a http instance.
     * 
     * @return Powerhouse\Gate\Http\Request
     */
    function request()
    {
        return Repository::get(Request::class);
    }
}

if (! function_exists('response')) {
    /**
     * Create a response instance.
     * 
     * @return Powerhouse\Gate\Http\Response
     */
    function response()
    {
        return Repository::get(Response::class);
    }
}

if (! function_exists('path')) {
    /**
     * Get the current path.
     * 
     * @param  string  $path
     * @return string
     */
    function path($path)
    {
        return http()->path($path);
    }
}

if (! function_exists('host')) {
    /**
     * Get the current path with host.
     * 
     * @param  string  $path
     * @return string
     */
    function host($path = null)
    {
        return http()->host($path);
    }
}
