<?php

    namespace Powerhouse\Freeway\Traits;

    use Powerhouse\Freeway\Holding;

    trait ErrorPusher
    {

        /**
         * Push an error to the array.
         * 
         * @param  string  $rule
         * @param  array  $attributes
         * @param  string  $field
         * @return bool
         */
        public function pushError($rule, $attributes, $field)
        {
            if (! isset(Holding::$errors[$field]))
                Holding::$errors[$field] = [];
            array_push(Holding::$errors[$field], __('validation.' . $rule, $attributes));

            return true;
        }

        /**
         * Get the errors.
         * 
         * @return array
         */
        public function getErrors()
        {
            return Holding::$errors;
        }

    }
