<?php

namespace Powerhouse\Routing;

use Powerhouse\Helpers\Traits\MagicCall;
use Contracts\Helpers\Traits\MagicCallInterface;
use Powerhouse\Routing\Mapper;

class Router implements MagicCallInterface
{
    use MagicCall;

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
     * Create a call.
     * 
     * @return Powerhouse\Routing\Mapper
     */
    public function createCaller()
    {
        self::$call = new Mapper();
        return self::$call;
    }

}
