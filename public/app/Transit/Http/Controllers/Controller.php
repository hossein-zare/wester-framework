<?php

    namespace App\Transit\Http\Controllers;

    class Controller
    {

        /**
         * Create a new controller instance.
         */
        public function __construct()
        {
            //
        }

        /**
         * Reject guests.
         */
        public function rejectGuests()
        {
            if (auth()->verified() === false) {
                redirect()->route('login')->do();
            }
        }

        /**
         * Reject users.
         */
        public function rejectUsers()
        {
            if (auth()->verified() === true) {
                redirect(auth()->home())->do();
            }
        }

    }
