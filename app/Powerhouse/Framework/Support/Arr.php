<?php

    namespace Powerhouse\Support;
    
    use Packages\Laravel\Support\Arr as ArrLaravel;
    use Exception;

    class Arr extends ArrLaravel
    {

        /**
         * Convert an array to an object.
         *
         * @param  array  $array
         * @param  string  $type
         * @return object
         */
        public static function toObject($array, $type = 'json')
        {
            // Array validation
            if (is_array($array)) {

                // Conversion type
                $types = ['object', 'json'];
                if (in_array($type, $types)) {

                    switch ($type) {
                        case 'object':
                            return (Object) $array;
                        break;
                        case 'json':
                            $json = json_encode($array);
                            return json_decode($json, false);
                        break;
                    }

                } else
                    throw new Exception('The passed arguments are invalid!');

            } else
                throw new Exception('The passed parameter is not an array!');
        }

        /**
         * Map arrays and get values.
         * 
         * @param  array  $array
         * @param  callback  $callback
         * @return array
         */
        public static function value(array $array, callable $callback)
        {
            $cache = [];
            foreach ($array as $key => $value)
                // Determine if the value is an array
                if (is_array($value) === true)
                    $cache[$key] = static::value($value, $callback);
                else
                    $cache[$key] = $callback($key, $value);

            return $cache;
        }

        /**
         * Collapse an array of arrays into a single array.
         * 
         * @param  array  $array
         * @return array
         */
        public static function collapse($array)
        {
            $results = [];

            foreach ($array as $values) {
                if (! is_array($values))
                    continue;

                $results = array_merge($results, $values);
            }

            return $results;
        }

        /**
         * Determine whether the key exists in the array.
         * 
         * @param  array  $array
         * @param  string  $key
         * @return bool
         */
        public static function exists($array, $key)
        {
            if (!static::accessible($array))
                return false;

            return array_key_exists($key, $array);
        }

        /**
         * Get deeply nested array elements (inherited from laravel).
         * 
         * @param  array  $key
         * @param  string  $key
         * @param  mixed  $default
         * @return mixed
         */
        public static function get($array, $key, $default = null)
        {
            if (!static::accessible($array))
                return $default;

            if (is_null($key))
                return $array;
            
            if (static::exists($array, $key))
                return $array[$key];

            if (strpos($key, '.') === false)
                return $array[$key] ?? $default;

            foreach (explode('.', $key) as $segment) {
                if (static::accessible($array) && $segment === '*')
                    return array_values($array);

                if (static::accessible($array) && static::exists($array, $segment))
                    $array = $array[$segment];
                else
                    return $default;
            }

            return $array;
        }

    }
