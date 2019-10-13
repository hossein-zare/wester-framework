<?php

    namespace Models;

    use Cactus\Model;

    class AuthSession extends Model
    {
        
        /**
         * Custom table name.
         * 
         * @var string
         */
        protected $table = 'auth_sessions';

        /**
         * Authenticate token
         * 
         * @param  string  $token
         * @return bool
         */
        public function scopeAuthenticateToken($token)
        {
            $length = $this->where('api_token', $token)->where('status', 1)->count();
            if ($length > 0)
                return true;

            return false;
        }

    }
