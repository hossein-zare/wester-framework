<?php

    namespace App\Transit\Http\Controllers\Auth;

    use App\Transit\Http\Controllers\Controller;
    use Powerhouse\Foundation\Auth\VerifiesAccount;

    class Verify extends Controller
    {
        use VerifiesAccount;

        /**
         * Take control over visits.
         * 
         * @return void
         */
        public function __construct()
        {
            $this->rejectUsers();
        }

    }
