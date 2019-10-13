<?php

    namespace Powerhouse\View\Tags;

    trait Method
    {

    	/**
    	 * {@inheritdoc}
    	 */
        protected function compileMethod($content)
        {
            return preg_replace_callback('/([@]+)method(\([^)]+\))/', function ($matches) {
                if (strlen($matches[1]) > 1)
                    return substr($matches[1], 0, -1) . 'method' . $matches[2];

                return '<input type="hidden" name="_method" value="<?php echo ' . $matches[2] . '; ?>">';
            }, $content);
        }

    }
