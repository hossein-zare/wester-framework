<?php

    namespace Powerhouse\View\Tags;

    use Powerhouse\View\BranchCompilers\GlobalCompiler;

    trait UnlessCondition
    {

        /**
         * {@inheritdoc}
         */
        protected function compileUnlessCondition($content)
        {
            $content = GlobalCompiler::toPHP('unless', $content, function ($matches) {
                return '<?php if(!' . trim($matches[1]) . '): ?>';
            });
            
            $content = str_replace('@endunless', '<?php endif; ?>', $content);

            return $content;
        }

    }
