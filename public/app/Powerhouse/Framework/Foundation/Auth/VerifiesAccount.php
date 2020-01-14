<?php

    namespace Powerhouse\Foundation\Auth;

    use App\Transit\Http\Handler\Request;
    use Models\User;
    use Models\AuthVerification;

    trait VerifiesAccount
    {

        /**
         * Process the given serial.
         * 
         * @param  string  $serial
         * @return \Powerhouse\Http\Redirect
         */
        public function process($serial)
        {
            // Get verification info
            $authVerification = AuthVerification::details($serial)->select('user_id')->get();

            if ($authVerification->count() === 1) {
                // Get the user id
                $user_id = $authVerification->user_id;

                // Delete the verification serial
                AuthVerification::serial($serial)->delete();

                // Verify the user
                User::verify($user_id);

                return redirect()->route('login')->with('success', [__('Your account has been activated successfully!')]);
            }
            
            return redirect()->route('login')->with('errors', [__('Your verification serial is invalid!')]);
        }

    }
