<?php

    namespace Powerhouse\View\BranchCompilers\Branches;

    class Stacks
    {

        /**
         * Stacks of the view.
         *
         * @var string
         */
        private static $stacks = [];

        /**
         * Compile push tags.
         * 
         * @param  string  $content
         * @param  string  $type
         * @return string
         */
        public function compile($content, $type)
        {
            if ($type === 'push') {
                $content = preg_replace_callback('/(?<!@)@push(\(\s*([^,)]+?)\s*\))(.*?)(@endpush)/s', function ($m) {

                    $stack_name = get_string_args($m[1])[0];
                    $this->push($stack_name, $m[3]);
                    return '';

                }, $content);
            }

            if ($type === 'prepend') {
                $content = preg_replace_callback('/(?<!@)@prepend(\(\s*([^,)]+?)\s*\))(.*?)(@endprepend)/s', function ($m) {

                    $stack_name = get_string_args($m[1])[0];
                    $this->prepend($stack_name, $m[3]);
                    return '';

                }, $content);
            }

            if ($type === 'get') {
                $content = preg_replace_callback('/(@stack)(\(\s*([^,)]+?)\s*\))/', function ($m ){

                    $stack_name = get_string_args($m[2])[0];
                    return $this->get($stack_name);

                }, $content);
            }

            return $content;

        }

        /**
         * Push data to the stack (Append).
         * 
         * @param  string  $name
         * @param  string  $value
         * @param  string  $type
         * @return void
         */
        protected function push($name, $value)
        {
            self::$stacks[$name][] = $value;
        }

        /**
         * Prepend data to the stack (Prepend).
         * 
         * @param  string  $name
         * @param  string  $value
         * @return void
         */
        protected function prepend($name, $value)
        {
            array_unshift(self::$stacks[$name], $value);
        }

        /**
         * Get stacks.
         * 
         * @param  string  $name
         * @return string|bool
         */
        protected function get($name)
        {
            if (isset(self::$stacks[$name])) {
                $content = '';
                foreach (self::$stacks[$name] as $stack) {
                    $content.= $stack;
                }

                return $content;
            } else {
                return false;
            }
        }

    }
