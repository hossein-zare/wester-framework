<?php

    namespace Powerhouse\Routing\Traits;

    use Powerhouse\Support\Str;
    use Exception;
    
    trait MethodParser
    {

        /**
         * The supported verbs.
         *
         * @var array
         */
        protected $verbs = ['ANY', 'GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'];
    
        /**
         * An Array of strings.
         *
         * @var array
         */
        protected $array = [];
    
        /**
         * Create an array from a string.
         *
         * @param  string|array  $value
         * @return array
         */
        protected function parseMethod($value)
        {
            $this->array = [];
            
            if (!is_array($value))
                array_push($this->array, $value);
            else
                $this->array = $value;

            return $this->toUpperCase();
        }
        
        /**
         * Convert an array to uppercase.
         *
         * @return array
         */
        protected function toUpperCase()
        {
            $this->array = array_map(function ($row) {
                return Str::upper($row);
            }, $this->array);
            
            return $this->isValid($this->array);
        }
        
        /**
         * Check if the method is valid.
         *
         * @param  string
         * @return array
         */
        protected function isValid($method)
        {
            foreach ($this->array as $row)
                if (!in_array($row, $this->verbs))
                    throw new Exception("The applied method on this route isn't valid!");
            
            return $this->array;
        }
    }
