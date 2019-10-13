<?php

    namespace Powerhouse\View\BranchCompilers;

    class GlobalCompiler
    {

        /**
         * Convert specific lines to php tags.
         * 
         * @param  string  $name
         * @param  string  $content
         * @param  callback|null  $callback
         * @return string
         */
        public static function toPHP($name, $content, $callback = null)
        {

            if ($callback === null) {
                $callback = function ($matches) use ($name) {
                    return '<?php '. $name . trim($matches[1]) . ': ?>';
                };
            }

            return preg_replace_callback('/@' . $name .'(.+)?/', $callback, $content);
        }

    }
