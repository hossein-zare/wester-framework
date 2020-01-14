<?php

    namespace Powerhouse\View\BranchCompilers\Branches;

    use Powerhouse\View\BranchCompilers\GlobalCompiler;

    class Switches
    {

        /**
         * Compile switch tags.
         *
         * @param  string  $content
         * @return string
         */
        public function compile($content)
        {
            return preg_replace_callback('/(?<!@)@switch(\(\s*([^,)]+?)\s*\))(.*?)(@endswitch)/s', function ($m) {

                $content = '<?php switch('.$m[2].'):'."\n";

                $content.= $m[3];
                /*
                $content = preg_replace("/(@case)(\([^)]+\))/", 'case ${2}: ?>', $content);
                */
                $content = GlobalCompiler::toPHP('case', $content, function ($matches) {
                    return 'case ' . trim($matches[1]) . ': ?>';
                });
                $content = preg_replace("/@break/", '<?php break;', $content);

                if (strpos($m[3], '@default') !== false) {
                    $content = preg_replace("/@default/", 'default: ?>', $content);
                    $content.= '<?php endswitch; ?>'."\n";
                } else {
                    $content.= 'endswitch; ?>'."\n";
                }

                return $content;

            }, $content);
        }

    }
