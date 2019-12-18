<?php

namespace Powerhouse\Gate\Http;

class Methods
{

    /**
     * Get the method name.
     * 
     * @return string
     */
    public function method()
    {
        return $_SERVER['REQUEST_METHOD'];
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
