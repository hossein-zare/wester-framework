<?php

    namespace Powerhouse\View\BranchCompilers\Branches;

    use Exception;

    class Sections
    {
        
        /**
         * Declaration of yield tags.
         *
         * @var array
         */
        protected $yieldTag = ['@yield','get'];
        
        /**
         * Declaration of section tags.
         *
         * @var array
         */
        protected $sectionTag = ['@section','save'];

        /**
         * All section tags contents.
         *
         * @var array
         */
        protected $section = [];

        /**
         * Compile provided tags.
         *
         * @param  string  $regex
         * @param  string  $replaceWith
         * @param  string  $content
         * @param  string  $tag
         * @return string
         */
        protected function compileTag($regex, $replaceWith, $content, $name, $tag)
        {
            if ($tag=='section') {
                $r = preg_replace_callback('/(?<!@)@section(\(\s*([^,)]+?)\s*\))(.*?)(@(endsection|show))/s', function($m) use ($content, $name){

                    $num_args = count_string_args($m[1]);
                    if ($num_args==1) {
                        // Addslashes before parantheses
                        $statement = addcslashes($m[1], '(');
                        $statement = addcslashes($statement, ')');

                            if (!isset($m[3]))
                                return false; // $r = 0;
                            elseif ($m[5] === 'show' && $name !== 'parent') {
                                $tag_name = get_string_args($m[1])[0];

                                $this->saveSection($tag_name, $m[3], 'parent');

                                $content_preg = $this->getSection($tag_name, null, 'parent') . $this->getSection($tag_name, null, 'html');
                                return $content_preg;
                            } elseif ($m[5] === 'endsection') {

                                $tag_name = get_string_args($m[1])[0];

                                if ($name === 'parent') {

                                    $this->saveSection($tag_name, $m[3], 'html');
                                    $content_preg = '';

                                } else {
                                    // In the extended template

                                    $this->saveSection($tag_name, $m[3], 'parent');

                                    // Get Parent Data
                                    if ($this->getSection($tag_name, 'parent') !== false){
                                        $m[3] = str_replace('@parent', $m[3], $this->getSection($tag_name, 'html'));
                                        $this->saveSection($tag_name, $m[3], 'html');
                                    }

                                    $content_preg = $this->getSection($tag_name, null, 'html');

                                }

                                return $content_preg;
                            } else
                                return false; // $r = 0;

                    } elseif ($num_args === 2)
                        return false;
                    else
                        throw new Exception("Error while parsing the number of arguments!");

                }, $content);

                if ($r !== false) {
                    $content = $r;
                }
            }

            $content = preg_replace_callback($regex, function ($matches) use ($replaceWith, $tag) {
                // Here we should apply conditions to the section parts
                if ($replaceWith === 'save') {
                    $args = get_string_args($matches[2]);
                    $this->saveSection($args[0], $args[1], 'html');

                    return '';
                } elseif ($replaceWith === 'get') {
                    $args = get_string_args($matches[2]);

                    return $this->getSection($args[0], (isset($args[1])? $args[1] : null), 'html');
                }
            }, $content);

            return $content;
        }

        /**
         * Compile tags.
         *
         * @param  string  $tag
         * @param  string  $replaceWith
         * @return string
         */
        public function compile($name, $content)
        {
            $tag = $this->yieldTag[0];
            $replaceWith = $this->yieldTag[1];
            $content = $this->compileTag("/({$tag})(\([^)]+\))/", $replaceWith, $content, $name, 'yield');

            $tag = $this->sectionTag[0];
            $replaceWith = $this->sectionTag[1];
            $content = $this->compileTag("/({$tag})(\([^)]+\))/", $replaceWith, $content, $name, 'section');

            return $content;
        }

        /**
         *  Save Section.
         *
         * @param  string  $name
         * @param  string  $value
         * @param  string  $type
         * @return void
         */
        protected function saveSection($name, $value, $type = 'html')
        {
            $this->section[$name][$type] = $value;
        }

        /**
         * Get Section.
         *
         * @param  string  $name
         * @param  string  $type
         * @return string
         */
        protected function getSection($name, $default = null, $type = 'html')
        {
            return (isset($this->section[$name][$type]) ? $this->section[$name][$type] : ($default? $default : false));
        }

    }
