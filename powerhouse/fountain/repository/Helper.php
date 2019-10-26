<?php

namespace Fountain\Repository;

class Helper
{

    /**
     * The repository.
     * 
     * @var array
     */
    private static $repository = [];

    /**
     * Indicates whether the given class exists in the repository.
     * 
     * @param  mixed  $class
     * @return bool
     */
    public static function exists($class)
    {
        return isset(self::$repository[self::getName($class)]);
    }

    /**
     * Get the name of the class.
     * 
     * @param  mixed  $class
     * @return string
     */
    private static function getName($class)
    {
        if (is_string($class))
            return basename($class);

        return get_class($class);
    }

    /**
     * Get the class instance from the repository or create a new instance of it.
     * 
     * @param  mixed  $class
     * @return mixed
     */
    public static function get($class)
    {
        if (self::exists($class))
            return self::$repository[self::getName($class)];

        self::$repository[self::getName($class)] = new $class();
        return self::get($class);
    }

}
