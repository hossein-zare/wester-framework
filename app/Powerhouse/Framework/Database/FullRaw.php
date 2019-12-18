<?php

    namespace Database;

    use Powerhouse\Interfaces\Database\FullRawQuery;

    class FullRaw implements FullRawQuery
    {

        /**
         * The statement holder.
         *
         * @var string
         */
        protected $statement = null;

        /**
         * The parameter holder.
         *
         * @var array
         */
        protected $parameters = null;
        
        /**
         * The constructor that assigns our statement.
         *
         * @param  string  $statement
         * @param  array  $parameters
         * @return void
         */
        public function __construct($statement, $parameters = [])
        {
            $this->statement = $statement;
            $this->parameters = $parameters;
        }

        /**
         * Return the raw statement.
         * 
         * @return string
         */
        public function statement($full = false)
        {
            if ($full === true)
                return [
                    'statement' => $this->statement,
                    'parameters' => $this->parameters
                ];

            return $this->statement;
        }

        /**
         * Return the raw statement.
         * 
         * @return string
         */
        public function __toString()
        {
            return (string) $this->statement();
        }
    }
