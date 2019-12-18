<?php

    namespace App\Transit\Http\Controllers\Auth;

    use App\Transit\Http\Controllers\Controller;
    use Models\User;
    use Powerhouse\Castles\Hash;
    use Powerhouse\Foundation\Auth\NewRegistration;

    class Register extends Controller
    {
        use NewRegistration;

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
         * Verificate the form.
         * 
         * @param  \App\Transit\Http\Handler\Request  $request
         * @return array
         */
        protected function validator($request)
        {
            $request->validate('post', [
                'first_name' => ['required', 'string', 'min:3', 'max:15', 'one_whitespace'],
                'last_name' => ['required', 'string', 'min:2', 'max:25', 'one_whitespace'],
                'email' => ['required', 'string', 'email', 'max:254', 'unique:users,email'],
                'password' => ['required', 'string', 'min:8', 'max:40', 'confirmed']
            ]);
        }

        /**
         * Create a new user.
         * 
         * @param  array  $data
         * @param  bool  $verified
         * @return \Powerhouse\Support\Collection
         */
        protected function create(array $data, bool $verified)
        {
            return User::create([
                'first_name' => ucfirst_only($data['first_name']),
                'last_name' => ucfirst_only($data['last_name']),
                'email' => strtolower($data['email']),
                'password' => Hash::make($data['password']),
                'verified' => (int) $verified
            ]);
        }

    }
