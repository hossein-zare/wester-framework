<?php

    namespace Powerhouse\View\Tags;

    use Powerhouse\View\BranchCompilers\GlobalCompiler;

    trait EmptyCondition
    {

        /**
         * {@inheritdoc}
         */
        protected function compileEmptyCondition($content)
        {
            $content = GlobalCompiler::toPHP('empty', $content, function ($matches) {
                return '<?php if(empty' . trim($matches[1]) . '): ?>';
            });
            
            $content = str_replace('@endempty', '<?php endif; ?>', $content);

            return $content;
        }

    }
