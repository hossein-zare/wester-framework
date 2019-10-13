<?php

    namespace Powerhouse\View\Tags;

    trait CodeBlocks
    {

        /**
         * {@inheritdoc}
         */
        protected function compileCodeBlocks($content)
        {
            return preg_replace_callback('/@code\((["\'])([^,)]+)\1(, *([^)]+))?\)(.*?)@endcode/s', function ($matches) {
                $languages = explode('|', $matches[2]);
                $langs = '';
                foreach ($languages as $language) {
                    $langs .= 'language-' . trim($language) . ' ';
                }

                $options = isset($matches[4]) ? json_decode($matches[4], true) : []; 
                $code = e($matches[5]);

                $class = isset($options['class']) ? $options['class'] : '';
                $style = isset($options['style']) ? $options['style'] : '';

                // Attributes
                $attributes = '';
                $attr = isset($options['attr']) ? $options['attr'] : null;
                if ($attr !== null) {
                    foreach ($attr as $name => $value) {
                        $attributes.= "{$name}=\"{$value}\" ";
                    }
                }

                return "<pre class=\"{$class}\" style=\"{$style}\" {$attributes}><code class=\"{$langs}\">{$code}</code></pre>";
            }, $content);
        }

    }
