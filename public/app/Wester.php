<?php

    // Register an Autoload function
    $wester = new Wester();
    spl_autoload_register([$wester, "autoload"]);

    use Powerhouse\Foundation\Castle\CastleMaker;

    class Wester
    {

        /**
         * The prefix for autoloading.
         *
         * @var string
         */
        private static $prefix = 'App\\';

        /**
         * Replace shortcuts.
         * 
         * @param  string  $class
         * @param  array  $shortcuts
         * @return string
         */
        private static function replaceShortcuts($class)
        {
            if (strpos($class, 'App\\') === 0)
                return $class;

            return strtr('^' . $class, CLASS_ALIASES);
        }

        /**
         * Get the full path of the class.
         * 
         * @param  string  $class
         * @return string
         */
        public static function getClassPath($class)
        {
            return (new self)->autoload($class, true);
        }

        /**
         * Autoload classes.
         *
         * @param  string  $class
         * @return void
         */
        public static function autoload($class, $getPath = false)
        {
            // Castle Classes
            if ($getPath === false && strpos($class, 'Castle') === 0) {
                CastleMaker::build($class);
                return;
            }

            // Convert shortcuts to real path
            $class = static::replaceShortcuts($class);

            // Project-specific namespace prefix
            $prefix = static::$prefix;

            // For backwards compatibility
            $dir = '';

            // Base directory for the namespace prefix
            $dir = $dir ?: __DIR__ . '/';

            // Does the class use the namespace prefix?
            $len = strlen($prefix);
            if ($getPath === false && strncmp($prefix, $class, $len) !== 0) {
                // No, move to the next registered class
                return;
            }

            // Get the relative class name
            $relativeClass = substr($class, $len);

            // Replace the namespace prefix with the base directory, replace namespace
            // separators with directory separators in the relative class name, append
            // with .php
            $file = rtrim($dir, '/') . '/' . str_replace('\\', '/', $relativeClass) . '.php';

            // Get the full path
            if ($getPath === true)
                return $file;

            // If the file exists, Require it
            if (file_exists($file)) {
                require_once $file;
            } else {
                throw new \Exception("File '<b>{$file}</b>' doesn't exist!");
            }
        }

    }
