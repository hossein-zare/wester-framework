<?php

    namespace Powerhouse\View\Tags;

    use Powerhouse\View\BranchCompilers\GlobalCompiler;

    trait IfCondition
    {

        /**
         * {@inheritdoc}
         */
        protected function compileIfCondition($content)
        {
            $content = GlobalCompiler::toPHP('if', $content);

            // Compile else & elseif tags
            $content = GlobalCompiler::toPHP('elseif', $content);
            $content = str_replace('@else', '<?php else: ?>', $content);
            $content = str_replace('@endif', '<?php endif; ?>', $content);

            return $content;
        }

    }
