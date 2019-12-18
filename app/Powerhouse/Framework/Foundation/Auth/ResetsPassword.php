<?php

    namespace Powerhouse\Foundation\Auth;

    use App\Transit\Http\Handler\Request;
    use Models\User;
    use Models\AuthReset;
    use Powerhouse\Castles\Mail;
    use Powerhouse\Support\Str;
    use Powerhouse\Foundation\Auth\Events\NewPasswordVerification;
    
    trait ResetsPassword
    {

        /**
         * Show the reset page.
         * 
         * @return string
         */
        public function form()
        {
            return view('auth/reset');
        }

        /**
         * Process the reset request.
         * 
         * @param  \App\Transit\Http\Handler\Request  $request
         * @return \Powerhouse\Http\Redirect
         */
        public function process(Request $request)
        {
            $this->validator($request);

            // Get user info
            $user = User::userDetails($request->post('email'))->select(['id', 'email'])->get();

            if ($user->count() === 1) {
                $serial = Str::random(28);

                // Get the user info
                $id = $user->id;
                $email = $user->email;

                // Create a reset serial
                AuthReset::create([
                    'user_id' => $id,
                    'serial' => $serial
                ]);

                // Send mail
                event(new NewPasswordVerification($email, $serial));

                return back()->with('success', [__('Please check your e-mail for password reset!')]);
            }

            return back()->with('errors', [__('E-mail address was wrong!')]);
        }

    }
