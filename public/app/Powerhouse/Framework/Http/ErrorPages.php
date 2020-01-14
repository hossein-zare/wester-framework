<?php

    namespace Powerhouse\Http;

    use App\Transit\Http\Handler\CustomErrorPages;

    class ErrorPages extends CustomErrorPages
    {

        /**
         * The response status code.
         *
         * @var int
        */
        protected $code;
        
        /**
         * The error message.
         *
         * @var string
        */
        protected $message;
        
        /**
         * Create a new instance of ErrorPages.
         * 
         * @param  int  $code
         * @param  string  $messages
         * @return void
        */
        public function __construct($code, $message)
        {
            $this->code = $code;
            $this->message = $message;
        }

    }
