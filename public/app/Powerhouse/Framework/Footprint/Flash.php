<?php

    namespace Powerhouse\Footprint;

    use Exception;

    class Flash
    {

        /**
         * Store the messages.
         * 
         * @var array|string
         */
        protected static $messages = [];

        /**
         * Get the flashed message as an object.
         * 
         * @param  string  $name
         * @return object
         */
        public static function get(string $name)
        {
            $messages = ray($name);
            self::$messages = $messages !== null ? $messages : [];
            
            return (new static);
        }

        /**
         * Count of messages.
         * 
         * @return int
         */
        public function count()
        {
            $this->isArray();
            return count(static::$messages);
        }

        /**
         * Determine whether theres any messages.
         * 
         * @return bool
         */
        public function any()
        {
            $this->isArray();
            return count(static::$messages) > 0 ? true : false;
        }

        /**
         * Check if the key exists in the messages.
         * 
         * @param  string  $name
         * @return bool
         */
        public function has(string $name)
        {
            $this->isArray();

            if (isset(static::$messages[$name]))
                return true;

            return false;
        }

        /**
         * Get all the messages.
         * 
         * @return mixed
         */
        public function all()
        {
            $this->isArray();

            $result = [];
            array_walk_recursive(static::$messages, function($a, $b) use (&$result){
                $result[] = $a;
            });

            return $result;
        }

        /**
         * Throw an error if the message isn't an array.
         * 
         * @return void
         */
        private function isArray()
        {
            if (is_array(static::$messages) === false) {
                session()->destroy();
                throw new Exception("Message flasher expects 2 paramaters, the first one is a string and the last one is an array of messages!");
            }
        }
    }
