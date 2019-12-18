<?php

namespace Powerhouse\Helpers\Traits;

trait MagicCall
{

    /**
     * The call repository
     * 
     * @var object
     */
    protected static $call;

    /**
     * {@inheritdoc}
     */
    abstract public function createCaller();

    /**
     * Call methods non-statically.
     * 
     * @param  string  $method
     * @param  array  $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        if (self::$call)
            return self::$call->{$method}(...$args);
        
        return $this->createCaller()->{$method}(...$args);
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
        if (self::$call)
            return self::$call->{$method}(...$args);
        
        return (new self)->createCaller()->{$method}(...$args);
    }

}