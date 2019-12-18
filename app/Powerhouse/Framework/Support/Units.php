<?php

    namespace Powerhouse\Support;

    use AppBundles\Olifolkerd\Convertor\Convertor;

    class Units
    {

        /**
         * Format bytes.
         * 
         * @param  int|float  $size
         * @param  int  $precision
         * @return string
         */
        public static function formatBytes($size, $precision = 2){
            $base = log($size, 1024);
            $suffixes = array('', 'KB', 'MB', 'GB', 'TB');

            return round(pow(1024, $base - floor($base)), $precision) .' '. $suffixes[floor($base)];
        }

        /**
         * Convert bytes to kilobytes.
         * 
         * @param  int|float  $size
         * @return int|float
         */
        public static function bytesToKilobytes($size)
        {
            return $size / 1024;
        }

        /**
         * Convert units.
         * 
         * @param  mixed[]  $args[]
         * @return \AppBundles\Olifolkerd\Convertor
         */
        public static function convert(...$args)
        {
            return new Convertor(...$args);
        }

    }
