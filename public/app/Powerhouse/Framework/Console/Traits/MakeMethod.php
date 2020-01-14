<?php

    namespace Powerhouse\Console\Traits;

    trait MakeMethod
    {

        /**
         * Create a method name from a string.
         * 
         * @param  string  $command
         * @return string
         */
        protected function toMethod($command)
        {
            if (strpos($command, ':') !== false) {
                $array = explode(':', $command);
                $name = '';
                for ($i = 0; $i < 2; $i++) {
                    $name .= $i == 1 ? ucfirst($array[$i]) : $array[$i];
                }

                return $name;
            } else {
                return $command;
            }
        }

    }