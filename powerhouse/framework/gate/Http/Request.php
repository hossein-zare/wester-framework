<?php

namespace Powerhouse\Gate\Http;

class Request
{

    /**
     * Repository.
     * 
     * @var array
     */
    private static $repository = [
        'input' => []
    ];
    
    /**
     * Indicates whether the object has already been initialized.
     * 
     * @var bool
     */
    private static $init = false;

    /**
     * Create a new instance of Request.
     */
    public function __construct()
    {
        if (! self::$init) {
            parse_str(file_get_contents("php://input"), self::$repository['input']);
            self::$init = true;
        }
    }

    /**
     * Get the http GET request variables.
     * 
     * @param  string  $name
     * @return string
     */
    public function get($name)
    {
        return $_GET[$name] ?? null;
    }

    /**
     * Get the http POST request variables.
     * 
     * @param  string  $name
     * @return string
     */
    public function post($name)
    {
        return $_POST[$name] ?? null;
    }

    /**
     * Get the http INPUT request variables.
     * 
     * @param  string  $name
     * @return string
     */
    public function input($name)
    {
        return self::$repository[$name] ?? null;
    }

    /**
     * Get the http FILE request data.
     * 
     * @param  string  $name
     * @return string
     */
    public function file($name)
    {
        return $_FILES[$name] ?? null;
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
     * Get the http request variables.
     * 
     * @param  string  $name
     * @return string
     */
    public function __get($name)
    {
        switch (http()->method()) {
            case 'GET':
                return $this->get($name);
                break;
            case 'POST':
                return $this->post($name);
                break;
            default:
                return $this->input($name);
        }
    }

}