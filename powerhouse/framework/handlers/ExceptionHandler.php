<?php

namespace Powerhouse\Handlers;

use Exception;

class ExceptionHandler
{

    /**
     * Handle custom errors.
     * 
     * @param  int  $severity
     * @param  string  $message
     * @param  string  $file
     * @param  int  $line
     * @return void
     */
    public function error($severity, $message, $file, $line)
    {
        while (ob_get_level() > 0)
            ob_end_clean();
        
        die($message . ' - '.$line);

        $this->showPage($severity, $message, $file, $line);
    }

    /**
     * Handle custom exceptions.
     * 
     * @param  mixed  $e
     * @return void
     */
    public function exception($e)
    {
        // $this->customExceptionInterfaces($e);

        while (ob_get_level() > 0)
            ob_end_clean();

        die($e->getMessage(). ' - '.$e->getLine());

        $this->showPage(1, $e->getMessage(), $e->getFile(), $e->getLine());
    }

}