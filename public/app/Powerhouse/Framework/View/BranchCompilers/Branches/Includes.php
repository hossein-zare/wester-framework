<?php

    namespace Powerhouse\View\BranchCompilers\Branches;

    use Powerhouse\View\Blade;

    class Includes
    {

        /**
         * Compile include tags.
         *
         * @param  string  $content
         * @return string
         */
        public function compile($content)
        {
            return preg_replace_callback('/@include(\(\s*([^)]+?)\s*\))/', function ($m) {
                return '<?php echo (new Powerhouse\View\Make())->make(get_defined_vars(), ' . $m[2] . '); ?>';
            }, $content);
        }

    }
