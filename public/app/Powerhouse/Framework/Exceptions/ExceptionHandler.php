<?php

    namespace Powerhouse\Exceptions;

    use Exception;
    use Powerhouse\Interfaces\Modal\ModalNotFound as ModalNotFoundInterface;

    class ExceptionHandler extends Exception
    {

        /**
         * Logs errors.
         * 
         * @var bool
         */
        protected $logErrors;

        /**
         * Create a new ExceptionHandler instance.
         * 
         * @param  bool  $logErrors
         * @return void
         */
        public function __construct($logErrors = true)
        {
            $this->logErrors = $logErrors;
        }

        /**
         * Handle custom errors.
         * 
         * @param  int  $severity
         * @param  string  $message
         * @param  string  $file
         * @param  int  $line
         * @return void
         */
        public function handleError($severity, $message, $file, $line)
        {
            while (ob_get_level() > 0)
                ob_end_clean();
            
            $this->showPage($severity, $message, $file, $line);
        }

        /**
         * Handle custom exceptions.
         * 
         * @param  \Exception  $e
         * @return void
         */
        public function handleException($e)
        {
            $this->customExceptionInterfaces($e);

            while (ob_get_level() > 0)
                ob_end_clean();

            $this->showPage(1, $e->getMessage(), $e->getFile(), $e->getLine());
        }

        /**
         * Show the error page.
         * 
         * @param  int  $severity
         * @param  string  $message
         * @param  string  $file
         * @param  int  $line
         * @return void
         */
        protected function showPage($severity, $message, $file, $line)
        {
            $this->debugMode($message, $file, $line);

            $code = $this->getCodeBlock($file, $line);

            include "./app/Powerhouse/Framework/Exceptions/Templates/ErrorView.php";

            // Shutdown
            die();
        }

        /**
         * Get the original file information.
         * 
         * @param  string  $file
         * @param  int  $line
         * @return array
         */
        protected function getOriginalInfo($file, $line)
        {
            $content = file_get_contents($file);
            if (preg_match('/<\?php \/\* (.*?) \*\/ \?>/', $content, $matches)) {
                $file = $matches[1];
                $line--;
            }

            return ['file' => $file, 'line' => $line];
        }

        /**
         * Log errors and show the 505 page on debug mode.
         * 
         * @param  string  $message
         * @param  string  $file
         * @param  int  $line
         * @return void
         */
        protected function debugMode($message, $file, $line)
        {
            global $config;

            if ($config['debug'] === false && $this->logErrors() === true) {
                error_log($message . ' - ' . $file . ' - on line ' . $line);
                abort(500);
            }
        }

        /**
         * Custom exception interfaces.
         * 
         * @return void
         */
        protected function customExceptionInterfaces($class)
        {
            if ($class instanceof ModalNotFoundInterface) {
                abort(404);
            }
        }

        /**
         * Indicates whether the error logger is active.
         * 
         * @return bool
         */
        protected function logErrors()
        {
            return $this->logErrors;
        }

        /**
         * Get the code block where the exception or error has been thrown.
         * 
         * @param  string  $file
         * @param  int  $line
         * @return object
         */
        protected function getCodeBlock($file, $line)
        {
            if (file_exists($file))
                return $this->getSpecificPart($file, $line);
        }

        /**
         * Get specific part of the file.
         * 
         * @param  string  $file
         * @param  int  $line
         * @return string
         */
        protected function getSpecificPart($file, $line)
        {
            $fileArray = file($file);

            // Get the original file information
            $info = $this->getOriginalInfo($file, $line);

            if ($file !== $info['file']) {
                $file = $info['file'];
                $line = $info['line'];
                array_shift($fileArray);
            }

            $min = 5;
            $max = 5;
            $preline = $line - $min;
            $postline = $line + $max;
            $fileline = count($fileArray);

            if ($preline <= 0)
                $preline = 1;

            if ($fileline < $postline)
                $postline = $fileline;

            $obj = new \stdClass();
            $obj->file = $file;
            $obj->line = $line;
            $obj->start = $preline;
            $obj->content = '';

            for ($i = $preline - 1; $i <= $postline - 1; $i++)
                $obj->content.= e($fileArray[$i]);

            return $obj;
        }

    }