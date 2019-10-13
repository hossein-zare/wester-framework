<?php

    namespace Powerhouse\View\Tags;

    trait TEcho
    {

        /**
         * {@inheritdoc}
         */
        protected function compileRawEcho($content)
        {
            return preg_replace("/(?<!@){ ?!!(.*?)!! ?}/", '<?php echo ${1} ?>', $content);
        }

        /**
         * {@inheritdoc}
         */
        protected function compileEncodedEcho($content)
        {
            return preg_replace("/(?<!@){{(.*?)}}/", '<?php echo e(${1}) ?>', $content);;
        }

    }
