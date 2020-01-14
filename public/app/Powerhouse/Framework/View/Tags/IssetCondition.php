<?php

    namespace Powerhouse\View\Tags;

    use Powerhouse\View\BranchCompilers\GlobalCompiler;

    trait IssetCondition
    {

        /**
         * {@inheritdoc}
         */
        protected function compileIssetCondition($content)
        {
            $content = GlobalCompiler::toPHP('isset', $content, function ($matches) {
                return '<?php if(isset' . trim($matches[1]) . '): ?>';
            });
            
            $content = str_replace('@endisset', '<?php endif; ?>', $content);

            return $content;
        }

    }
