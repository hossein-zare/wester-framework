<?php

namespace Fountain\Loader;

class Config
{

    /**
     * The configuration.
     * 
     * @var array
     */
    private static $configuration = [];

    /**
     * Create a new instane of Config.
     */
    public function __construct()
    {
        if (count(self::$configuration) !== 0)
            return;

        self::$configuration['app'] = require_once '../config/app.php';
    }

    /**
     * Get the configuration as a property.
     * 
     * @param  string  $name
     * @return object
     */
    public function __get($name)
    {
        return toObject(self::$configuration[$name]);
    }

    /**
     * Get the configuration statically.
     * 
     * @param  string  $method
     * @param  array  $args
     * @return object
     */
    public static function __callStatic($method, $args)
    {
        return toObject(self::$configuration[$method]);
    }

    /**
     * Get the configuration non-statically.
     * 
     * @param  string  $method
     * @param  array  $args
     * @return object
     */
    public function __call($method, $args)
    {
        return toObject(self::$configuration[$method]);
    }

}
