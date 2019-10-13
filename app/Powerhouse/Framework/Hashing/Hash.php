<?php

    namespace Powerhouse\Hashing;

    use Exception;

    class Hash
    {

        /**
         * Create a new hasher instance with a custom algorithm.
         * 
         * @param  int  $algorithm
         */
        public function algorithm($algorithm)
        {
            $this->validate($algorithm);
            $this->algorithm = $algorithm;

            return $this;
        }

        /**
         * Get the algorithm.
         * 
         * @return int
         */
        protected function getAlgorithm()
        {
            return $this->alogrithm ?? $this->setAlgorithm();
        }

        /**
         * Set the algorithm.
         * 
         * @return int
         */
        protected function setAlgorithm()
        {
            global $config_hashing;
            $algorithm = $config_hashing['algorithm'];

            $this->validate($algorithm);
            $this->algorithm = $algorithm;
            
            return $this->algorithm;
        }

        /**
         * Validate the algorithm.
         * 
         * @param  int  $algorithm
         * @return bool
         */
        protected function validate($algorithm)
        {
            if (! is_int($algorithm) || $algorithm > 4 || $algorithm < 1) {
                throw new Exception("The given algorithm <b>'{$algorithm}'</b> is invalid!");
            }
        }

        /**
         * Hash the given value.
         *
         * @param  string  $value
         * @param  array   $options
         * @return string
         */
        public function make($value, array $options = [])
        {
            return password_hash($value, $this->getAlgorithm(), $options);
        }

        /**
         * Rehash the given value.
         *
         * @param  string  $value
         * @param  array   $options
         * @return string
         */
        public function rehash($value, array $options = [])
        {
            return password_needs_rehash($value, $this->getAlgorithm(), $options);
        }

        /**
         * Get information about the hashed value.
         * 
         * @param  string  $hashedValue
         * @return array
         */
        public function info($hashedValue)
        {
            return password_get_info($hashedValue);
        }

        /**
         * Check the given plain value against a hash.
         *
         * @param  string  $value
         * @param  string  $hashedValue
         * @return bool
         */
        public function check($value, $hashedValue)
        {
            return password_verify($value, $hashedValue);
        }

    }
