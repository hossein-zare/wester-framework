<?php

    namespace Powerhouse\Exceptions;
    
    use Exception;

    class PayloadException
    {
    
        public function __construct($message)
        {
            /**
             * Encryption payloads contain sensitive data and lead users to them.
             * if the payload can't be authorized we have to log the user out and destory all sessions and cookies.
             */
            auth()->logout();
            session()->destroy();
            cookie()->destroy();

            throw new Exception($message);
        }

    }
