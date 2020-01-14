<?php

    namespace Powerhouse\Foundation\Auth;

    use App\Transit\Http\Handler\Request;
    use Models\User;
    use Powerhouse\Castles\Hash;

    trait AuthenticatesUser
    {

        /**
         * Show the login page.
         * 
         * @return string
         */
        public function form()
        {
            return view('auth/login');
        }

        /**
         * Process the login request.
         * 
         * @param  \App\Transit\Http\Handler\Request  $request
         * @return \Powerhouse\Http\Redirect
         */
        public function process(Request $request)
        {
            $this->formValidation($request);

            $user = $this->getUserDetails($request);

            if ($user->count() === 1)
                if ($this->verifyPassword($request->post('password'), $user->password) === true) {
                    // Determine if the user is verified
                    if ((int) $user->verified !== 1) {
                        return back()->with('errors', [__('Please verify your account!')]);
                    }

                    // Log the user in
                    $id = (int) $user->id;
                    auth()->login($id);

                    return redirect(auth()->home());
                }
            
            return back()->with('errors', [__('E-mail address or password was wrong!')]);
        }

        /**
         * Specify the identifier.
         * 
         * @return string
         */
        protected function identifier()
        {
            return 'email';
        }

        /**
         * Verify the password.
         * 
         * @param  string  $userPassword
         * @param  string  $modalPassword
         * @return bool
         */
        protected function verifyPassword(string $userPassword, string $modalPassword)
        {
            return Hash::check($userPassword, $modalPassword);
        }

        /**
         * Get the user's details.
         * 
         * @param  \App\Transit\Http\Handler\Request  $request
         * @return \Powerhouse\Support\Collection
         */
        protected function getUserDetails(Request $request)
        {
            $identifier = $this->identifier();
            return User::userDetails($request->post($identifier))->get();
        }

        /**
         * Validate the user login request.
         *
         * @param  \App\Transit\Http\Handler\Request  $request
         * @return void
         */
        protected function formValidation(Request $request)
        {
            $request->validate('post', [
                $this->identifier() => ['required', 'string', 'email'],
                'password' => ['required', 'string'],
                'remember' => ['boolean']
            ]);
        }

    }
