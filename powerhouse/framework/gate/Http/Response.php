<?php

namespace Powerhouse\Gate\Http;

class Response
{

    /**
     * Create a new instance of Response.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get all headers.
     * 
     * @return array
     */
    public function headers()
    {
        return getallheaders();
    }

    /**
     * Get a header.
     * 
     * @param  string  $name
     * @return string
     */
    public function get(string $name = null)
    {
        if (! $name)
            return $_SERVER;

        return $_SERVER[$name] ?? null;
    }

    /**
     * Set a header.
     * 
     * @param  string  $name
     * @param  string  $value
     * @param  array  $args
     * @return void
     */
    public function set(string $name, $value, ...$args)
    {
        header("{$name}: $value", ...$args);
    }

    /**
     * Set or Get a header.
     * 
     * @param  string  $name
     * @param  string  $value
     * @param  array  $args
     * @return void
     */
    public function header(string $name, $value = null, ...$args)
    {
        if (! $value)
            return $this->get($name);
        
        $this->set($name, $value, ...$args);
    }

    /**
     * Set as json.
     * 
     * @return Powerhouse\Gate\Http\Response
     */
    public function json()
    {
        $this->header('Content-Type', 'application/json');

        return $this;
    }

    /**
     * Set as text.
     * 
     * @return Powerhouse\Gate\Http\Response
     */
    public function text()
    {
        $this->header('Content-Type', 'text/plain');

        return $this;
    }

    /**
     * Set as html.
     * 
     * @return Powerhouse\Gate\Http\Response
     */
    public function html()
    {
        $this->header('Content-Type', 'text/html');

        return $this;
    }

    /**
     * Set or Get HTTP Response Code.
     * 
     * @param  int  $code
     * @return int
     */
    public function status($code = null)
    {
        if (! $code)
            return http_response_code();
        
        http_response_code($code);

        return $this->status();
    }

}