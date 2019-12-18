<?php

namespace Powerhouse\Gate;

use Powerhouse\Helpers\Traits\MagicCall;

class Http
{
    use MagicCall;

    /**
     * Create a new instance of Http.
     */
    public function __construct()
    {
        //
    }

    /**
     * Create a call.
     * 
     * @return Powerhouse\Gate\Http\Methods
     */
    protected function createCaller()
    {
        self::$call = new Http\Methods();
        return self::$call;
    }

}
