<?php

namespace Fountain\Loader;

use Powerhouse\Handlers\ExceptionHandler;
use Powerhouse\Routing\Router;

class Tower
{

    /**
     * Initialize a list of files.
     * 
     * @param  array  $files
     */
    public static function watch($files)
    {
        foreach ($files as $file) {
            self::init($file);
        }
    }

    /**
     * Initialize files.
     * 
     * @param  string  $class
     */
    public static function init($file)
    {
        $words = explode('-', $file);

        // Uppercase first letter of every word except the first one
        $firstWord = $words[0];
        $words = array_map(function ($value) use ($firstWord) {
            return $value === $firstWord ? $value : ucfirst($value);
        }, $words);

        self::{implode($words)}();
    }

    /**
     * Register the exception handler.
     */
    public static function exceptionHandler()
    {
        // Register the error handler.
        set_error_handler(function (...$args) {
            (new ExceptionHandler())->error(...$args);
        });

        // Register the exception handler.
        set_exception_handler(function (...$args) {
            (new ExceptionHandler())->exception(...$args);
        });

        // The error and exception handlers couldn't catch anything,
        // If there's any error aborting the current process is required.
        if (error_get_last() !== null) {
            die("500 error in the tower exception handler.");
        }
    }

    /**
     * Watch the Web Routes.
     */
    public static function webRoutes()
    {
        require_once '../app/routes/web.php';
    }

    /**
     * Watch the API Routes.
     */
    public static function apiRoutes()
    {
        require_once '../app/routes/api.php';
    }

    /**
     * Run the router.
     * 
     * @return void
     */
    public static function startRouting()
    {
        Router::serve();
    }

}
