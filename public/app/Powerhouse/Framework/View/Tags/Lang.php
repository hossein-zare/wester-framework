<?php

    namespace Powerhouse\View\Tags;

    trait Lang
    {

    	/**
    	 * {@inheritdoc}
    	 */
        protected function compileLang($content)
        {
            return preg_replace('/(@lang)(\([^)]+\))/', '<?php echo __${2}; ?>', $content);
        }

    }
