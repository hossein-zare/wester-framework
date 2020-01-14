<?php

    namespace Powerhouse\Foundation\Auth\Events;
    
    use Powerhouse\Event\Traits\Dispatchable;

    class NewPasswordVerification
    {
        use Dispatchable;

        /**
         * The user's email address.
         * 
         * @var string
         */
        public $email;

        /**
         * The serial for verification.
         * 
         * @var string
         */
        public $serial;

        /**
         * Create a new event instance.
         *
         * @param  string  $email
         * @param  string  $serial
         * @return void
         */
        public function __construct(string $email, string $serial)
        {
            $this->email = $email;
            $this->serial = $serial;
        }

    }
