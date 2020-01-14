<?php

    namespace Powerhouse\Foundation\Auth;

    use App\Transit\Http\Handler\Request;
    use Models\User;
    use Models\AuthReset;
    use Powerhouse\Castles\Hash;

    trait ChangesPassword
    {

        /**
         * Process the serial and show the page.
         * 
         * @param  string  $serial
         * @return \Powerhouse\Http\Redirect
         */
        public function form($serial)
        {
            // Get reset details
            $authReset = $this->getDetails($serial);

            if ($authReset->count() === 1)
                return view('auth/change-password')->with('serial', $serial);

            return redirect()->route('login')->with('errors', [__('The reset serial was invalid!')]);
        }

        /**
         * Process the change password request.
         * 
         * @param  \App\Transit\Http\Handler\Request  $request
         * @param  string  $serial
         * @return \Powerhouse\Http\Redirect
         */
        public function process(Request $request, $serial)
        {
            $this->formValidation($request);

            $authReset = $this->getDetails($serial);
            if ($authReset->count() === 1) {
                // Get the user id
                $user_id = $authReset->user_id;

                // Delete the serial
                AuthReset::serial($serial)->delete();

                // Change password
                $password = $this->passwordHash($request->post('password'));
                User::changePassword($user_id, $password);

                return redirect()->route('login')->with('success', [__('Your password was changed successfully!')]);
            }

            return redirect()->route('login')->with('errors', [__('The reset serial was invalid!')]);
        }

        /**
         * Get the details.
         * 
         * @param  string  $serial
         * @return \Powerhouse\Support\Collection
         */
        protected function getDetails($serial)
        {
            return AuthReset::details($serial)->select('user_id')->get();
        }

        /**
         * Hash the password.
         * 
         * @param  string  $userPassword
         * @return string
         */
        protected function passwordHash(string $userPassword)
        {
            return Hash::make($userPassword);
        }

        /**
         * Validate the password change request.
         *
         * @param  \App\Transit\Http\Handler\Request  $request
         * @return void
         */
        protected function formValidation(Request $request)
        {
            $request->validate('post', [
                'password' => ['required', 'string', 'min:8', 'max:40', 'confirmed']
            ]);
        }

    }
