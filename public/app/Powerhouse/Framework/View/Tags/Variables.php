<?php

    namespace Powerhouse\View\Tags;

    trait Variables
    {

    	/**
    	 * {@inheritdoc}
    	 */
        protected function compileVariables($content)
        {
            return preg_replace('/@define\(([^)]+)\)/', '<?php ${1} ?>', $content);
        }

    }
