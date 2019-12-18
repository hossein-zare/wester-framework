<?php

namespace Powerhouse\Routing;

use Powerhouse\Routing\Mapper;

class Router
{

    /**
     * The mapper instance.
     * 
     * @var Powerhouse\Routing\Mapper
     */
    protected static $mapper;

    /**
     * Create a router instance.
     * 
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Create a mapper instance.
     * 
     * @return Powerhouse\Routing\Mapper
     */
    protected function createMapper()
    {
        self::$mapper = new Mapper();
        return self::$mapper;
    }

    /**
     * Call methods non-statically.
     * 
     * @param  string  $method
     * @param  array  $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        if (self::$mapper)
            return self::$mapper->{$method}(...$args);
        
        return $this->createMapper()->{$method}(...$args);
    }

    /**
     * Call methods statically.
     * 
     * @param  string  $method
     * @param  array  $args
     * @return mixed
     */
    public static function __callStatic($method, $args)
    {
        if (self::$mapper)
            return self::$mapper->{$method}(...$args);
        
        return (new self)->createMapper()->{$method}(...$args);
    }

}
