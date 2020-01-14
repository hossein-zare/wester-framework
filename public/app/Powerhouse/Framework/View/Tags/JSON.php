<?php

    namespace Powerhouse\View\Tags;

    trait JSON
    {

    	/**
    	 * {@inheritdoc}
    	 */
        protected function compileJSON($content)
        {
            return preg_replace('/@json\(([^)]+)\)/', '<?php echo json_encode(${1}); ?>', $content);
        }

    }
