<?php

    namespace App\Transit\Http\Controllers\Auth;

    use App\Transit\Http\Controllers\Controller;
    use Powerhouse\Foundation\Auth\ChangesPassword;

    class ChangePassword extends Controller
    {
        use ChangesPassword;

        /**
         * Take control over visits.
         * 
         * @return string
         */
        public function __construct()
        {
            $this->rejectUsers();
        }

    }
