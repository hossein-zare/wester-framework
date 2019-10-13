<?php

    namespace Models;

    use Cactus\Model;

    class AuthReset extends Model
    {

        /**
         * Custom table name.
         * 
         * @var string
         */
        protected $table = 'auth_resets';

        /**
         * Returns information.
         * 
         * @param  string  $serial
         * @return \Cactus\QueryBuilder\Builder
         */
        public function scopeDetails($serial)
        {
            return $this->where('serial', $serial)->first();
        }

        /**
         * Add the serial to the query.
         * 
         * @param  string  $serial
         * @return bool
         */
        public function scopeSerial($serial)
        {
            return $this->where('serial', $serial);
        }

    }
