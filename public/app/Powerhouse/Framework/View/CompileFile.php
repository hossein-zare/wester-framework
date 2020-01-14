<?php

    namespace Powerhouse\View;

    use Powerhouse\View\BranchCompilers\Branches\Freezers;
    use Powerhouse\View\BranchCompilers\Branches\Switches;
    use Powerhouse\View\BranchCompilers\Branches\Includes;
    use Powerhouse\View\BranchCompilers\Branches\Loops;
    use Powerhouse\View\BranchCompilers\Branches\Stacks;
    use Powerhouse\Support\Extendable\Spark as SparkDirective;

    class CompileFile
    {
        use Tags\Extension,
            Tags\TEcho,
            Tags\Tokens,
            Tags\Method,
            Tags\Lang,
            Tags\CodeBlocks,
            Tags\Variables,
            Tags\JSON,
            Tags\IfCondition,
            Tags\UnlessCondition,
            Tags\IssetCondition,
            Tags\EmptyCondition;

        /**
         * Compile tags.
         * 
         * @param  string  $content
         * @return string
         */
        protected function compile($content)
        {
            return $this->parseTags($content);
        }
        
        /**
         * Parse the spark tags.
         * 
         * @param  string  $content
         * @return string
         */
        protected function parseTags($content)
        {
            // Comment the path of the view
            $content = $this->commentPath($content);

            $content = (new Includes())->compile($content);
            $content = (new Freezers())->compile($content);
            $content = (new Switches())->compile($content);

            $content = $this->compileExtension($content);
            $content = $this->compileRawEcho($content);
            $content = $this->compileEncodedEcho($content);
            $content = $this->compileCsrfToken($content);
            $content = $this->compileDisposableToken($content);
            $content = $this->compileMethod($content);
            $content = $this->compileLang($content);
            $content = $this->compileCodeBlocks($content);
            $content = $this->compileVariables($content);
            $content = $this->compileJSON($content);
            $content = $this->compileIfCondition($content);
            $content = $this->compileUnlessCondition($content);
            $content = $this->compileIssetCondition($content);
            $content = $this->compileEmptyCondition($content);

            $content = $this->getDirectives($content);
            $content = preg_replace("/@{{(.*?)}}/", '{{${1}}}', $content);
            $content = (new Loops())->compile($content);
            $content = compileStacks()->compile($content, 'push');
            $content = compileStacks()->compile($content, 'prepend');
            $content = compileStacks()->compile($content, 'get');

            return $content;
        }

        /**
         * Comment the path of the view.
         * 
         * @param  string  $content
         * @return string
         */
        protected function commentPath($content)
        {
            $view = $this->getViewPath();
            $comment = "<?php /* {$view} */ ?>\n";
            $content = $comment . $content;

            return $content;
        }

        /**
         * Get directives.
         * 
         * @param  string  $content
         * @return string
         */
        protected function getDirectives($content)
        {
            $castleSpark = new SparkDirective();
            $directives = $castleSpark->getDirectives();
            foreach ($directives as $name => $callback) {
                $content = preg_replace_callback("/(?<!@)@{$name}(\((.*?)\))/", function ($matches) use ($callback) {
                    array_splice($matches, 0, 1);
                    return call_user_func($callback, ...$matches);
                }, $content);
            }

            return $content;
        }

    }
