<?php

    namespace Powerhouse\View\Castle;

    use Exception;
    use Powerhouse\Castles\Root;

    class Composer
    {
        
        /**
         * The list of composers.
         * 
         * @var array
         */
        protected static $composers = [];

        /**
         * View composer.
         * 
         * @param  string|array  $views
         * @param  callback|string  $action
         * @return void
         */
        public function composer($views, $action)
        {
            $views = toArray($views);

            if (!is_string($action) && !is_callable($action))
                throw new Exception("The second parameter has to be a string or closure!");

            $this->addComposer($views, $action);
        }

        /**
         * Add the composer to the list.
         * 
         * @param  string|array  $views
         * @param  callback|string  $action
         * @return void
         */
        private function addComposer($views, $action)
        {
            $array = [
                'views' => $views,
                'action' => $action
            ];

            self::$composers[] = $array;
        }

        /**
         * Parse a class based composer.
         * 
         * @param  string  $class
         * @param  mixed  $arguments
         * @return void
         */
        private function parseClass($class, $instance)
        {
            (new $class)($instance);
        }

        /**
         * Parse a closure based composer.
         * 
         * @param  callback  $class
         * @param  mixed  $arguments
         * @return void
         */
        private function parseClosure($closure, $instance)
        {
            $closure($instance);
        }

        /**
         * Compose.
         * 
         * @param  string  $view
         * @return void
         */
        public function compose($view)
        {
            foreach (self::$composers as $composer) {
                if (Root::arrBranchOf($view, $composer['views'])) {
                    $action = $composer['action'];

                    if (is_string($action) === true)
                        $this->parseClass($action, $this);
    
                    elseif (is_callable($action) === true)
                        $this->parseClosure($action, $this);
                }
            }
        }

    }
