<?php

    namespace Powerhouse\Http\Validation;

    use Powerhouse\Freeway\Traits\ErrorPusher;

    class Validator extends Injection
    {
        use ErrorPusher;
        use Traits\InputType,
            Traits\Fields, Traits\FileMimeTypes;

        /**
         * HTTP Request Method.
         * 
         * @var string
         */
        protected $method = null;

        /**
         * Cache.
         * 
         * @var array
         */
        protected $cache = [];

        /**
         * Field Value.
         * 
         * @var string
         */
        protected $fieldValue = false;

        /**
         * Error container.
         * 
         * @var array
         */
        protected static $errors = [];

        /**
         * Data types.
         * 
         * @var array
         */
        protected $dataTypes = ['integer', 'float', 'string', 'array', 'file', 'file_array', 'image'];

        /**
         * First step validation.
         * 
         * @param  string  $method
         * @param  array  $rulesArray
         * @param  bool  $flash
         * @return array
         */
        public function validate(string $method, array $rulesArray, $flash = true)
        {
            // Get the method
            $this->method = $method;

            // Inject the rules
            $this->injectRules($rulesArray);

            // Flash error messages
            if ($flash === true && count(request()->getErrors()) > 0)
                back()->with('errors', request()->getErrors())->withInput()->do();

            return request()->getErrors();
        }

        /**
         * {@inheritDoc}
         */
        protected function makeValidator(string $method, array $rulesArray, array $messages = [], $flash = false)
        {
            // Push messages
            app()->customize('custom', ['validation' => $messages]);

            // Get the method
            $this->method = $method;

            // Inject the rules
            $this->injectRules($rulesArray);

            // Reset messages
            app()->unset('custom');

            // Flash error messages
            if ($flash === true && count(request()->getErrors()) > 0) {
                back()->with('errors', request()->getErrors())->withInput()->do();
            }

            return request()->getErrors();
        }

        /**
         * Make an advanced validator.
         * 
         * @param  string  $method
         * @param  array  $rulesArray
         * @param  array  $messages
         * @param  bool  $flash
         * @return array
         */
        public static function make(string $method, array $rulesArray, array $messages = [], $flash = false)
        {
            return ( new static() )->makeValidator($method, $rulesArray, $messages, $flash);
        }

    }
