<?php

    namespace Powerhouse\Storage;

    use Exception;

    class FileSystem extends Attributes
    {

        /**
         * The disk name.
         * 
         * @var string
         */
        protected $disk;

        /**
         * Get the storage ready.
         * 
         * @return void
         */
        public function __construct()
        {
            $this->disk();
        }

        /**
         * Get the disk.
         * 
         * @param  string  $name
         * @return $this
         */
        public function disk($name = null)
        {
            global $config_filesystems;

            if ($name !== null)
                $this->disk = array_to_object($config_filesystems['disks'][$name]);
            else {
                $default = $config_filesystems['default'];
                $this->disk = array_to_object($config_filesystems['disks'][$default]);
            }

            return $this;
        }

        /**
         * Explore custom directories.
         * 
         * @param  string  $name
         * @return $this
         */
        public function explore($name)
        {
            $this->disk = array_to_object(['root' => $name]);
            return $this;
        }

        /**
         * Marge the address.
         * 
         * @param  string  $path
         * @return string
         */
        protected function merge($path)
        {
            $disk = $this->disk;
            return trim($disk->root, '/') . '/' . trim($path, '/');
        }

    }
