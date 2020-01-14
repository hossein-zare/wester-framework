<?php

    namespace Powerhouse\Console;

    abstract class Operations extends Commands
    {
        use Traits\CommandsInfo, Traits\MakeMethod;

        /**
         * Serve the application.
         * 
         * @param  int  $argc
         * @param  array  $argv
         * @return void
         */
        protected function serveApplication(int $argc, array $argv)
        {
            // No arguments passed to the console app.
            if ($argc <= 1) {
                $this->shutdown("Please enter the operation name!", 'warning');
            }

            // Check the passed arguments
            $isset = isset($this->commands[$argv[1]]);
            if ($isset === false || ($isset === true && $this->commands[$argv[1]]['argc'] !== $argc)) {
                $this->shutdown("Wrong arguments!", 'warning');
            }

            // Run commands
            $this->commands($argv);
        }

        /**
         * Determine whether the value is a string.
         * 
         * @param  mixed  $value
         * @return void|bool
         */
        protected function isString($value)
        {
            if (is_string($value) === false) {
                $this->shutdown("Your value must be a string!", 'red');
            }

            return true;
        }

        /**
         * File Stream.
         * 
         * @param  string  $path
         * @param  string  $mode
         * @param  callback  $callback
         * @return bool|string
         */
        protected function fileStream(string $path, string $mode, callable $callback)
        {
            $file = fopen($path, $mode);
            $output = $callback($file);
            fclose($file);

            return $output;
        }
    }
