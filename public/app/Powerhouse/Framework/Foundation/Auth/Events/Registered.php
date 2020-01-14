<?php

    namespace Powerhouse\Foundation\Auth\Events;
    
    use Powerhouse\Event\Traits\Dispatchable;

    class Registered
    {
        use Dispatchable;

        /**
         * Set whether verificated is required.
         * 
         * @var bool
         */
        public $verificationRequired;

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
         * @param  bool  $verificationRequired
         * @param  string  $email
         * @param  string  $serial
         * @return void
         */
        public function __construct(bool $verificationRequired, string $email, string $serial)
        {
            $this->verificationRequired = $verificationRequired;
            $this->email = $email;
            $this->serial = $serial;
        }

    }
