<?php

    namespace App\Transit\Http\Controllers\Auth;

    use App\Transit\Http\Controllers\Controller;
    use Powerhouse\Foundation\Auth\ResetsPassword;

    class Reset extends Controller
    {
        use ResetsPassword;

        /**
         * Take control over visits.
         * 
         * @return void
         */
        public function __construct()
        {
            $this->rejectUsers();
        }

        /**
         * Validates the form.
         * 
         * @param  \App\Transit\Http\Handler\Request  $request
         * @return void
         */
        protected function validator($request)
        {
            $request->validate('post', [
                'email' => ['required', 'string', 'email', 'max:254', 'exists:users,email']
            ]);
        }

    }
