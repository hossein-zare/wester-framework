<?php

namespace Powerhouse\Gate;

use Powerhouse\Helpers\Traits\MagicCall;
use Contracts\Helpers\Traits\MagicCallInterface;

class Http implements MagicCallInterface
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
    public function createCaller()
    {
        self::$call = new Http\Methods();
        return self::$call;
    }

}
