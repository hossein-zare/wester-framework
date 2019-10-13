<?php

    namespace Powerhouse\View\BranchCompilers\Branches;

    class Freezers
    {

        /**
         * Compile freezer tags.
         *
         * @param  string  $content
         * @return string
         */
        public function compile($content)
        {
            return preg_replace_callback('/(?<!@)@freezer(.*?)(@endfreezer)/s', function ($m) {
                
                // Raw Echos
                $content = preg_replace("/(?<!@){{(.+)}}/", '@{{${1}}}', $m[1]);
                return $content;

            }, $content);
        }

    }
