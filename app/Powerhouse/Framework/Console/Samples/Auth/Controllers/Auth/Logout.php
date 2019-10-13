<?php

    namespace App\Transit\Http\Controllers\Auth;

    use App\Transit\Http\Controllers\Controller;

    class Logout extends Controller
    {

        /**
         * Process the logout request.
         * 
         * @return \Powerhouse\Http\Redirect
         */
        public function process()
        {
            auth()->logout();
            return redirect(auth()->fallback());
        }

    }
