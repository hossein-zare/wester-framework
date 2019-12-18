<?php

    namespace Powerhouse\View\Tags;

    trait Tokens
    {

        /**
         * {@inheritdoc}
         */
        protected function compileCsrfToken($content)
        {
            return preg_replace_callback('/([@]+)csrf/', function ($matches) {
                if (strlen($matches[1]) > 1)
                    return substr($matches[1], 0, -1) . 'csrf';

                return '<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">';
            }, $content);
        }

        /**
         * {@inheritdoc}
         */
        protected function compileDisposableToken($content)
        {
            return preg_replace_callback('/([@]+)disposable_token(\([^)]+\))/', function ($matches) {
                if (strlen($matches[1]) > 1)
                    return substr($matches[1], 0, -1) . 'disposable_token' . $matches[2];

                return '<input type="hidden" name="_token_disposable" value="<?php echo disposable_token()->getToken' . $matches[2] . '; ?>">';
            }, $content);
        }

    }
