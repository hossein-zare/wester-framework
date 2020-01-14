<?php

    namespace Powerhouse\Repository;

    class Repository
    {

        /**
         * The list of stored values.
         * 
         * @var array
         */
        protected static $repository = [
            'cache' => []
        ];

        /**
         * Store data in the repository.
         * 
         * @param  string  $name
         * @param  string  $identifier
         * @param  mixed  $data
         * @return void
         */
        public function store(string $name, string $identifier, $data)
        {
            self::$repository[$name][$identifier] = $data;
        }

        /**
         * Get the data from the repository.
         * 
         * @param  string  $name
         * @param  string  $identifier
         * @return mixed
         */
        public function get($name, $identifier)
        {
            return self::$repository[$name][$identifier];
        }

        /**
         * Inquire whether the expected data exists.
         * 
         * @param  string  $name
         * @param  string  $identifier
         * @return bool
         */
        public function exists($name, $identifier)
        {
            return isset(self::$repository[$name][$identifier]);
        }

        /**
         * Inquire whether the expected data exists.
         * 
         * @param  string  $name
         * @param  string  $identifier
         * @return bool
         */
        public function has($name, $identifier)
        {
            return $this->exists($name, $identifier);
        }

    }
