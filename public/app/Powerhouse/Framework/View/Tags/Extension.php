<?php

    namespace Powerhouse\View\Tags;
    
    use Powerhouse\View\BranchCompilers\Branches\Sections;

    trait Extension
    {

        /**
         * Layouts of the view.
         *
         * @var array
         */
        protected $layouts = [];
        
        /**
         * Yields of the view.
         *
         * @var array
         */
        protected $yields = [];

        /**
         * Compile extensions.
         * 
         * @param   string  $content
         * @return  string
         */
        protected function compileExtension($content)
        {
            $this->getExtentions($content, 1);
            return $this->renderExtentions();
        }

        /**
         * Delete extendings of the view.
         *
         * @param  string  $content
         * @return string
         */
        protected function deleteExtensions($content)
        {
            return preg_replace('/@extends\((["\'])([^)]+)\1\)/s', '', $content);
        }

        /**
         * Get extendings of the view.
         *
         * @param  string  $content
         * @param  int  $i
         * @return string
         */
        protected function getExtentions($content, $i = 2)
        {
            if ($i==1)
            $this->layouts['parent'] = $this->deleteExtensions($content);

            if (preg_match('/@extends\((["\'])([^)]+)\1\)/s', $content, $matches)) {
                $layout_name = str_replace('.', '/', $matches[2]);
                $layout = $this->getContents($this->getPath() . $layout_name . $this->getSuffix());

                $this->layouts[$matches[2]] = $this->deleteExtensions($layout);

                return $this->getExtentions($layout);
            } else {
                return;
            }
        }
        
        /**
         * Render extendings of the view.
         *
         * @return string
         */
        protected function renderExtentions()
        {
            $layouts = $this->layouts;
            $SectionCompiler = new Sections();

            $content = [];
            foreach ($layouts as $name => $data) {
                $data = $SectionCompiler->compile($name, $data);
                $content[] = trim($data);
            }

            return implode('', $content);
        }
        
    }
