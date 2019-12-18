<?php

    namespace Powerhouse\Http\Cache\File;

    use Powerhouse\Http\Request;

    class FileCache
    {
        use Helper;

        /**
         * The input name
         * 
         * @var string
         */
        protected $inputName;

        /**
         * Create the object
         */
        public function __construct($name)
        {
            $this->inputName = $name;
        }

        /**
         * Get the file
         * 
         * @return  object
         */
        protected function getFile()
        {
            return request()->all()['files'][$this->inputName];
        }

    }
