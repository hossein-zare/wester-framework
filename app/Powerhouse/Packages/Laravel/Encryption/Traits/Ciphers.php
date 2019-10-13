<?php

    /**
     * @with    Compatibility Changes
     * @author  Laravel
     */

    namespace Packages\Laravel\Encryption\Traits;

    trait Ciphers
    {
        
        /**
         * Determine if the given key and cipher combination is valid.
         *
         * @param  string  $key
         * @param  string  $cipher
         * @return bool
         */
        public static function supported($key, $cipher)
        {
            $length = mb_strlen($key, '8bit');

            return ($cipher === 'AES-128-CBC' && $length === 16) ||
                   ($cipher === 'AES-256-CBC' && $length === 32);
        }
        
        /**
         * Create a new encryption key for the given cipher.
         *
         * @param  string  $cipher
         * @return string
         */
        public static function generateKey($cipher)
        {
            return random_bytes($cipher === 'AES-128-CBC' ? 16 : 32);
        }

    }
