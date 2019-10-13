<?php

    namespace App\Transit\Http\Handler;

    class CustomErrorPages
    {
   
        /**
         * A sample of custom error page for 404
         * 
         * @return  string
        */
        protected function _404()
        {
            return display('errors/error_404');
        }
        
        /**
         * Show the error page
         *
         * @return  string
        */
        public function show()
        {
            /*
                Catch 404 error
                ----------------------------------------
                if ($this->code === 404) {
                    return $this->_404();
                }
            */
            
            return display('errors/default_error_page', ['code' => $this->code, 'message' => $this->message]);
        }

    }
