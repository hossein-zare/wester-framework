<?php

    namespace Powerhouse\Services;

    use Powerhouse\Routing\Route;
    use App\Transit\Http\Handler\Request;
    use Powerhouse\Services\ServiceProvider;
    use Powerhouse\Castles\Response;
    use AppBundles\CodeCreator\HtmlCreator;
    use Powerhouse\Exceptions\ExceptionHandler;

    class WatchTower extends Route
    {

        /**
         * Supervise the routes.
         * 
         * @return void
         */
        protected static function routing()
        {
            $route = route();

            // Load cached routes
            $route->loadCache();

            // Web routes
            $route->seek("web");
            require_once './app/Routes/Web.php';
            
            // Api routes
            $route->seek("api");
            require_once './app/Routes/API.php';

            // Parse routes
            $route->parseRoutes();
        }

        /**
         * Run the opening service providers.
         * 
         * @return void
         */
        protected static function opening()
        {
            (new ServiceProvider())->opening();
        }
        
        /**
         * Run the closing service providers.
         * 
         * @return void
         */
        protected static function closing()
        {
            (new ServiceProvider())->closing();

            session()->destroy('flash');
            session()->regenerate(true);
        }

        /**
         * Start running the service providers.
         * 
         * @return void
         */
        public static function services()
        {
            self::opening();
            self::routing();
            self::closing();
        }

        /**
         * Register exception handlers.
         * 
         * @return void
         */
        public static function registerExceptionHandlers()
        {
            set_error_handler(function (...$args) {
                (new ExceptionHandler())->handleError(...$args);
            });
            set_exception_handler(function (...$args) {
                $exception = new ExceptionHandler();
                $exception->handleException(...$args);
            });

            // The error and exception handlers couldn't catch anything,
            // If there's any error aborting the current process is required.
            if (error_get_last() !== null) {
                abort(500);
            }
        }

        /**
         * Register shutdown.
         * 
         * @param  callback|closure  $callback
         * @return void
         */
        public static function registerShutdown($callback)
        {
            register_shutdown_function(function () use ($callback) {
                $callback();
            });
        }

        /**
         * Present the debug info.
         * 
         * @param  bool  $active
         * @return void
         */
        public static function debugInfo($active = true)
        {
            global $config;
            
            if (! $config['debug'])
                return;
            
            if (! $active)
                return;

            if (! Response::expectsJson()) {
                $diff = round(microtime(true) - START_TIME, 3);

                $htmlCreator = new HtmlCreator();

                $left = $htmlCreator->createElem('div', "<b>Debug Info</b> | Load time: {$diff}s", [
                    'style' => 'float: left;'
                ]);
                $right = $htmlCreator->createElem('div', "<a style=\"color: #565656;\" href=\"https://framework.wester.ir/\"><b>Wester Framework | 1.0</b></a>", [
                    'style' => 'float: right;'
                ]);
                $clearfix = $htmlCreator->createElem('div', null, [
                    'style' => 'clear: both;'
                ]);
                $footer = $htmlCreator->createElem('div', $left.$right.$clearfix, [
                    'style' => 'font-size: 14px;'
                ]);
                $footer = $htmlCreator->createElem('div', $footer, [
                    'style' => 'padding: 20px 40px;'
                ]);
                $footer = $htmlCreator->createElem('div', $footer, [
                    'style' => 'position: fixed; left: 0; bottom: 0; background-color: #e9ecef; color: #565656; width: 100%; font-family: tahoma; z-index: 1000;'
                ]);
                $footer = $footer . $htmlCreator->createElem('div', null, [
                    'style' => 'margin-bottom: 56px;'
                ]);

                $htmlCreator->output($footer);
            }
        }

    }
