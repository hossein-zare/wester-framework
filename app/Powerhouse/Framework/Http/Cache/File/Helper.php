<?php

    namespace Powerhouse\Http\Cache\File;

    use Powerhouse\Castles\Storage;

    trait Helper
    {

        /**
         * Get the file extension
         * 
         * @return  string
         */
        public function extension()
        {
            if (is_array($this->getFile()['name'])) {
                $extensions = [];

                foreach ($this->getFile()['name'] as $name)
                    $extensions[] = strtolower(pathinfo($name, PATHINFO_EXTENSION));

                return $extensions;
            } else
                return strtolower(pathinfo($this->getFile()['name'], PATHINFO_EXTENSION));
        }

        /**
         * Get the file mime type
         * 
         * @return  string
         */
        public function mime()
        {
            if (is_array($this->getFile()['tmp_name'])) {
                $extensions = [];

                foreach ($this->getFile()['tmp_name'] as $name)
                    $extensions[] = mime_content_type($name);

                return $extensions;
            } else
                return mime_content_type($this->getFile()['tmp_name']);
        }


        /**
         * Get the file extension
         * 
         * @return  string
         */
        public function name()
        {
            if (is_array($this->getFile()['name'])) {
                return array_map(function ($file) {
                    return pathinfo($file, PATHINFO_FILENAME);
                }, $this->getFile()['name']);

            } else
                return pathinfo($this->getFile()['name'], PATHINFO_FILENAME);
        }

        /**
         * Get the file extension
         * 
         * @return  string
         */
        public function size()
        {
            return $this->getFile()['size'];
        }

        /**
         * Upload the given file
         * 
         * @param   string  $folder
         * @param   string  $disk
         * @return  string
         */
        public function store($folder, $disk = null)
        {
            $file = $this;

            if ($disk === null)
                return Storage::setDir($folder)->putFile($file);
            
            return Storage::disk($disk)->setDir($folder)->putFile($file);
        }

        /**
         * Upload the given file (Advanced)
         * 
         * @param   string  $folder
         * @param   string  $disk
         * @return  string
         */
        public function storeAs($folder, $name, $disk = null)
        {
            $file = $this;

            if ($disk === null)
                return Storage::setDir($folder)->putFileAs($file, $name);
            
            return Storage::disk($disk)->setDir($folder)->putFileAs($file, $name);
        }

        /**
         * Get the full file information as an array
         * 
         * @return  array
         */
        public function raw()
        {
            return $this->getFile();
        }

        /**
         * Get the file tmp name
         * 
         * @return  array|string
         */
        public function get()
        {
            return $this->getFile()['tmp_name'];
        }

    }
