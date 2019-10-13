<?php

    namespace Powerhouse\Console;

    class ConsoleApplication extends Operations
    {

        /**
         * Run the application.
         * 
         * @param  int  $argc
         * @param  array  $argv
         * @return void
         */
        public function serve(int $argc, array $argv)
        {
            $this->serveApplication($argc, $argv);
        }

        /**
         * Print a message.
         * 
         * @param  string  $message
         * @param  string  $color
         * @return void
         */
        public function publish(string $message, $color = null)
        {
            $colors = [
                'red' => "\e[0;31m%s\e[0m",
                'green' => "\e[0;32m%s\e[0m",
                'warning' => "\e[1;31m%s\e[0m"
            ];

            if ($color !== null) {
                $message = sprintf($colors[$color], $message);
            }

            echo "\t" . $message . "\n";
        }

        /**
         * Shut down the console.
         * 
         * @param  string  $message
         * @param  string  $color
         * @return void
         */
        public function shutdown($message = null, $color = null)
        {
            if ($message !== null)
                $this->publish($message, $color);
            exit();
        }

    }