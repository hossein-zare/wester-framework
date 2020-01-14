<?php

    namespace Powerhouse\Routing\Traits;

    trait ParameterParser
    {
    
        /**
         * Parameter Regex.
         *
         * @var string
         */
        protected $parameterRegex = "/{([a-z_]+)[?]{0}}/";

        /**
         * Optional Parameter Regex.
         *
         * @var string
         */
        protected $optionalParamaterRegex = "/{([a-z_]+)[?]{1}}/";

        /**
         * List of all taken uris.
         *
         * @var array
         */
        protected $takenUris = [
            'app', 'api'
        ];
    
        /**
         * Prepare The URI.
         *
         * @param  string|array  $value
         * @return array
         */
        protected function prepareUri($value)
        {
            $value = trim($value, '/');
            return $this->toArray('/', $value);
        }
        
        /**
         * Prepare The URL.
         *
         * @param  string|array  $value
         * @return array
         */
        protected function prepareUrl($value)
        {
            global $config;

            $path = trim($config['path'], '/');
            
            $value = urldecode($value);
            $value = strtok($value, '?');
            $value = trim($value, '/');
            $value = str_replace($path, '', $value);
            $value = trim($value, '/');
            
            static::$request_uri = $value;

            return $this->toArray('/', $value);
        }
        
        /**
         * Number of Optional parameters in the URI.
         *
         * @param  array  $uri
         * @return int
         */
        protected function optionalParametersNum($uri)
        {
            if (is_array($uri)) {
                $i = 0;
                foreach ($uri as $parameter)
                    if (preg_match($this->optionalParamaterRegex, $parameter))
                        $i++;
                return $i;
            } else
                return preg_match_all($this->optionalParamaterRegex, $uri, $matches);
        }
        
        /**
         * Determine if the given string is taken.
         *
         * @param  string  $uri
         * @return array
         */
        protected function takenUri($uri) {
            if (in_array($uri, $this->takenUris) === true)
                return true;
            
            return false;
        }
        
        /**
         * Convert a string to an array.
         *
         * @param  string  $separator
         * @param  string  $value
         * @return array
         */
        protected function toArray($separator, $value)
        {
            return explode($separator, $value);
        }

    }
