<?php

    namespace Powerhouse\Http\Validation\Traits;

    trait InputType
    {

        /**
         * Get the type of the field.
         * 
         * @param  string  $field
         * @param  array  $rules
         * @return string|null
         */
        protected function getDefinedType($field, $rules)
        {
            // Get the similar keys
            $definedType = array_intersect($rules, $this->dataTypes);

            // Reindex the array
            $definedType = array_values($definedType);
            
            if (strpos($field, '.') !== false) {
                // Determine whether the parent is a file
                $parentName = $this->onlyName($field);
                if (isset($this->cache[$parentName]['inputType']) && $this->cache[$parentName]['inputType'][0] === 'file_array') {
                    $definedType = (count($definedType) > 0) ? $definedType[0] : null;
                    if ($definedType === 'image')
                        return 'image';
                    return 'file';
                }
            }

            return (count($definedType) > 0) ? $definedType[0] : null;
        }

        /**
         * Determine whether the field is a sub-field.
         * 
         * @param  string  $field
         * @return bool
         */
        public function subType(string $field)
        {
            if (strpos($field, '.') !== false)
                return true;
            
            return false;
        }

        /**
         * Determine whether the field is a parent-field.
         * 
         * @param  string  $field
         * @return bool
         */
        public function isParent($field)
        {
            return !$this->cache[$field]['subType'];
        }

        /**
         * Convert a value to an array.
         * 
         * @param  string  $value
         * @return array
         */
        public function valueToArray($value)
        {
            return [$value];
        }

        /**
         * Get the input type.
         * 
         * @param  string  $field
         * @return string|null
         */
        public function inputType(string $field)
        {
            $request = request();

            // Determine if the input is a file
            if (isset($_FILES[$field]['name']) === true) {
                $multiple = determine_multiple_files($_FILES[$field]);
                if ($multiple === true) {
                    foreach ($_FILES[$field]['error'] as $key => $error)
                        if ($error != 0)
                            return null;
                        else
                            return 'file_array';
                } else {
                    if ($_FILES[$field]['size'] == 0 && $_FILES[$field]['error'] != 0)
                        return null;
                    else 
                        return 'file';
                }
            }

            // Get the raw value of the field
            $rawValue = $request->retrieveByMethod($this->method, $field);

            // Return null if the raw value is null
            if ($rawValue === null)
                return null;

            // Array
            if (is_array($rawValue) === true)
                return 'array';

            // Integer
            $value = (int) $rawValue;
            if ((string) $value === $rawValue && is_int($value))
                return 'integer';

            // Float
            $value = (float) $rawValue;
            if ((string) $value === $rawValue && is_float($value))
                return 'float';

            // String
            return 'string';
        }

        /**
         * Get the sub input type.
         * 
         * @param  string  $field
         * @return string|null
         */
        public function subInputType($field)
        {
            $parentName = $this->onlyName($field);

            // Determine whether the parent is a file
            if (isset($this->cache[$parentName]['inputType']) && $this->cache[$parentName]['inputType'][0] === 'file_array') {
                return ['file'];
            }

            $input = [$parentName => request()->retrieveByMethod($this->method, $this->onlyName($field))];
            $array = array_get($input, $field, null);

            // Types
            $types = [];

            if (is_string($array))
                return [$this->getSingleType($array)];

            if ($array !== null) {
                
                foreach ($array as $item)
                    $types[] = $this->getSingleType($array);

                return $types;

            } else
                return [null];
        }

        /**
         * Get a single item type.
         * 
         * @param  mixed  $item
         * @return string|null
         */
        public function getSingleType($item)
        {
            // Return null if the raw value is null
            if ($item === null)
                return null;

            // Array
            if (is_array($item) === true)
                return 'array';

            // Integer
            $value = (int) $item;
            if ((string) $value === $item && is_int($value))
                return 'integer';

            // Float
            $value = (float) $item;
            if ((string) $value === $item && is_float($value))
                return 'float';

            // String
            return 'string';
        }

        /**
         * Convert a variable to another type.
         * 
         * @param  string  $value
         * @param  string  $type
         * @return string|int|float|array
         */
        public function convertType($value, $type)
        {
            switch ($type) {
                case 'integer':
                    return (int) $value;
                break;

                case 'float':
                    return (float) $value;
                break;

                case 'string':
                    return (string) $value;
                break;
                
                default:
                    return $value;
            }
        }

    }
