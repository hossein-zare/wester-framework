<?php

    namespace Powerhouse\Routing;
  
    use App\Transit\Http\Handler\Request;
    use Powerhouse\Routing\Route;
  
    abstract class View
    {

        /**
         * Register a new view or route.
         *
         * @return $this
         */
        public function view()
        {
            $num = func_num_args();
            $arguments = func_get_args();
            
            // Route
            if ($num === 3 || $num === 2) {
                if ($num === 3 && is_array($arguments[2]))
                    return $GLOBALS['structure']['route']->get($arguments[0], function() use ($arguments) {

                        view($arguments[1]);

                    })->with($arguments[2]);

                if ($num === 2 && is_string($arguments[1]))
                    return $GLOBALS['structure']['route']->get($arguments[0], function() use ($arguments) {

                        view($arguments[1]);
                        
                    });
            }
            
            // View
            if ($num === 2 || $num === 1) {
                if ($num === 2 && is_array($arguments[1])) {

                    static::$actionType = 'view';
                    spark()->config($arguments[0]);
                    return $this->with($arguments[1]);

                }

                if ($num === 1 && is_string($arguments[0])) {

                    static::$actionType = 'view';
                    spark()->config($arguments[0]);
                    return $this;

                }
            }
        }

    }
