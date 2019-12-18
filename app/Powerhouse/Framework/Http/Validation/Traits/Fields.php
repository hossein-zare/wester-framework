<?php

    namespace Powerhouse\Http\Validation\Traits;

    trait Fields
    {

        /**
         * Get the name of the field.
         * 
         * @param  string  $field
         * @param  string  $customAttribute
         * @return string
         */
        public function onlyName($field, $customAttribute = false)
        {
            // Get the name without extra characters
            if (strpos($field, '.') !== false)
                $field = explode('.', $field)[0];

            // Custom Validation Attributes
            if ($customAttribute === true) {
                $name = __('validation.attributes.' . $field);
                $field = $name !== null ? $name : $field;
            }

            return $field;
        }

        /**
         * Get the name of the field (Nested).
         * 
         * @param  string  $field
         * @param  string  $customAttribute
         * @return string
         */
        public function onlyCustomName($field, $customAttribute = false)
        {
            // Get the name without extra characters
            if (strpos($field, '*') !== false)
                $field = str_replace('.*', '', $field);

            if (strpos($field, '.') === false)
                return $this->onlyName($field, $customAttribute);

            // Custom Validation Attributes
            if ($customAttribute === true) {
                $name = __('validation.nested.attributes.' . $field);
                $field = $name !== null ? $name : $field;
            }

            return $field;
        }

    }
