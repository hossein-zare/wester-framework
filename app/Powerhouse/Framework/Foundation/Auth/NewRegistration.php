<?php

    namespace Powerhouse\Foundation\Auth;

    use App\Transit\Http\Handler\Request;
    use Models\AuthVerification;
    use Powerhouse\Castles\Auth;
    use Powerhouse\Support\Str;
    use Powerhouse\Foundation\Auth\Events\Registered;

    trait NewRegistration
    {

        /*
         * Show the registration page.
         * 
         * @return string
         */
        public function form()
        {
            return view('auth/register');
        }

        /**
         * Process the registration request.
         * 
         * @param  \App\Transit\Http\Handler\Request  $request
         * @return \Powerhouse\Http\Redirect
         */
        public function process(Request $request)
        {
            $this->validator($request);

            // Verification required
            $verificationRequired = Auth::verificationRequired();

            // Create the user and get the details
            $new_user = $this->create($request->all()['post'], !$verificationRequired);
            $id = (int) $new_user->id;
            $email = $new_user->email;

            // Log in if verification is not required
            if ($verificationRequired === false) {
                // Log the newly created user in
                auth()->login($id);
            } else {
                $serial = Str::random(28);

                // Create a verification serial
                $this->createVerificationSerial($id, $serial);
            }

            // Send mail
            event(new Registered($verificationRequired, $email, $serial ?? ''));

            if ($verificationRequired === true) {
                return redirect()->route('login')->with('success', [__('Please check your email for the verification link!')]);
            }

            return redirect(auth()->home())->with('success', [__('You have successfully registered!')]);
        }

        /**
         * Create a verification serial.
         * 
         * @param  int  $id
         * @param  string  $serial
         * @return null
         */
        protected function createVerificationSerial(int $id, string $serial)
        {
            AuthVerification::create([
                'user_id' => $id,
                'serial' => $serial
            ]);
        }

    }
