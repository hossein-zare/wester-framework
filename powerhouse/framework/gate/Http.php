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
        $occurrences = substr_count(self::$redirectUri, '/');

        $prefix = '.';
        for ($i = 0; $i < $occurrences; $i++)
            $prefix .= '/..';

        return $prefix;
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
     * Get current path.
     * 
     * @param  string  $name
     * @return array
     */
    public function path($name)
    {
        $name = trim($name, '/');
        return self::$pathPrefix . "/{$name}";
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
