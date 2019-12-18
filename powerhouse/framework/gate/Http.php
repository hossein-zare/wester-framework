<?php

namespace Powerhouse\Gate;

class Http
{

    /**
     * The redirect URI.
     * 
     * @var string
     */
    public static $redirectUri;

    /**
     * The path prefix
     * 
     * @var string
     */
    public static $pathPrefix;

    /**
     * Indicates whether the object has already been initialized.
     * 
     * @var bool
     */
    private static $init = false;

    /**
     * Create a new instance of Http.
     */
    public function __construct()
    {
        if (! self::$init) {
            self::$redirectUri = $this->getRedirectUri();
            self::$pathPrefix = $this->getPathPrefix();

            self::$init = true;
        }
    }

    /**
     * Get the redirect uri.
     * 
     * @return string
     */
    private function getRedirectUri()
    {
        return preg_replace('/\/{2,}/', '/', response()->get('REDIRECT_URL'));
    }

    /**
     * Get the path prefix.
     * 
     * @return string
     */
    private function getPathPrefix()
    {
        $occurrences = substr_count($this->getRedirectUri(), '/');

        $prefix = '.';
        for ($i = 0; $i < $occurrences; $i++)
            $prefix .= '/..';

        return $prefix;
    }

    /**
     * Get the host.
     * 
     * @return string
     */
    private function getHost()
    {
        $protocol = response()->get('HTTPS') ? 'https://' : 'http://';
        return $protocol . response()->get('SERVER_NAME');
    }

    /**
     * Get the current path.
     * 
     * @param  string  $path
     * @return array
     */
    public function path($path)
    {
        $path = trim($path, '/');
        return self::$pathPrefix . "/{$path}";
    }

    /**
     * Get the current path with host.
     * 
     * @param  string  $path
     * @return array
     */
    public function host($path = null)
    {
        $path = trim($path, '/');
        return $this->getHost() . "/{$path}";
    }

    /**
     * Indicates whether the request is an ajax request.
     * 
     * @return bool
     */
    public function isAjax()
    {
        return ! empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Indicates whether the request is an api request.
     * 
     * @param  string  $uri
     * @return bool
     */
    public function isApi($uri = null)
    {
        $uri = trim($uri ?: $this->getRedirectUri(), '/');
        if (substr($uri, 0, 3) === config()->app->api_prefix)
            return true;

        return false;
    }

    /**
     * Get the method name.
     * 
     * @return string
     */
    public function method()
    {
        return response()->get('REQUEST_METHOD');
    }

    /**
     * Get the request methods
     * 
     * @return Powerhouse\Gate\Http\Request
     */
    public function request()
    {
        return request();
    }

    /**
     * Get the response methods
     * 
     * @return Powerhouse\Gate\Http\Response
     */
    public function response()
    {
        return response();
    }

}
