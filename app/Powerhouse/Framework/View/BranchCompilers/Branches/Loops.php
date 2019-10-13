<?php

    namespace Powerhouse\View\BranchCompilers\Branches;

    use Powerhouse\View\BranchCompilers\GlobalCompiler;

    class Loops
    {

        /**
         * Order of loops.
         *
         * @var array
         */
        private $order = ['foreach','for','while'];

        /**
         * Compile loop tags.
         * 
         * @param  string  $content
         * @return string
         */
        public function compile($content)
        {
            foreach($this->order as $tag){
                $content = GlobalCompiler::toPHP($tag, $content);
                $content = str_replace("@end{$tag}", "<?php end{$tag}; ?>", $content);
            }
            
            return $content;
        }

    }
