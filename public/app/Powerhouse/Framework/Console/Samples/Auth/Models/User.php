<?php

    namespace Models;

    use Cactus\Model;

    class User extends Model
    {

        /**
         * Mass assignable fields.
         * 
         * @var array
         */
        protected $fillable = [
            'first_name',
            'last_name',
            'email',
            'password',
            'verified',
        ];

        /**
         * Returns information.
         * 
         * @param  string  $email
         * @return \Cactus\QueryBuilder\Builder
         */
        public function scopeUserDetails($email)
        {
            return $this->where("email", $email)->first();
        }

        /**
         * Verify a user.
         * 
         * @param  int  $user_id
         * @return void
         */
        public function scopeVerify($user_id)
        {
            $this->where('id', $user_id);
            $this->verified = 1;

            $this->update();
        }

        /**
         * Change a user's password.
         * 
         * @param  int  $user_id
         * @param  string  $password
         * @return void
         */
        public function scopeChangePassword($user_id, $password)
        {
            $this->where('id', $user_id);
            $this->password = $password;

            $this->update();
        }
    }
