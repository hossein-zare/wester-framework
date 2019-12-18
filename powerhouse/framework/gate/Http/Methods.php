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
     * Define the method.
     * 
     * @param  string  $method
     */
    public function from($method)
    {
        
    }

}
