<?php

    namespace App\Transit\Http\Controllers\Auth;

    use App\Transit\Http\Controllers\Controller;
    use Powerhouse\Foundation\Auth\AuthenticatesUser;

    class Login extends Controller
    {
        use AuthenticatesUser;

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
