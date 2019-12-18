<?php

namespace Powerhouse\Routing;

use Carbon\Carbon;
use Powerhouse\Gate\Http;

class Find
{

    /**
     * Serve the matching route.
     */
    public function serve()
    {
        // foreach (static::$routes as $route) {
        //     var_dump(http()->isApi($route['uri']));
        // }
        printf("Now: %s", Carbon::now());
    }

}
