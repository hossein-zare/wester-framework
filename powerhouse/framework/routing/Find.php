<?php

namespace Powerhouse\Routing;

use Powerhouse\Gate\Http;

class Find
{

    /**
     * Serve the matching route.
     */
    public function serve()
    {
        // self::$routes[0]['func']();
        var_dump(Http::method());
    }

}
