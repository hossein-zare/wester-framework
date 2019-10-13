<?php

    namespace Powerhouse\Footprint;

    use Exception;
    use Powerhouse\Support\Str;

    class DisposableToken
    {

        /**
         * Provide a disposable token.
         * 
         * @param  string  $name
         * @param  bool  $new
         * @return string
         */
        public function get($name, $new = false)
        {
            if ($new === true || isset($_SESSION['_token_disposable']) === false) {
                $token = Str::random(30);

                if (isset($_SESSION['_token_disposable']) === true) {
                    if (is_string($_SESSION['_token_disposable']))
                        $session = $this->add($name, $token);
                    else
                        $session = $this->create($name, $token);
                } else {
                    $session = $this->create($name, $token);
                }
            } else
                $session = $this->getToken($name);

            return $session;
        }

        /**
         * Create a new token.
         * 
         * @param  string  $name
         * @param  string  $token
         * @return void
         */
        private function create($name, $token)
        {
            $session = [$name => $token];
            $session = json_encode($session);
            $session = crypto()->encrypt($session);
            $_SESSION['_token_disposable'] = $session;

            return $token;
        }

        /**
         * Push a new token to the session.
         * 
         * @param  string  $name
         * @param  string  $token
         * @return void
         */
        private function add($name, $token)
        {
            $session = crypto()->decrypt($_SESSION['_token_disposable']);
            $session = json_decode($session, true);
            $session[$name] = $token;
            $session = json_encode($session);
            $session = crypto()->encrypt($session);
            $_SESSION['_token_disposable'] = $session;

            return $token;
        }

        /**
         * Pop a token by name.
         * 
         * @param  string  $name
         * @return void
         */
        private function getToken($name)
        {
            $session = crypto()->decrypt($_SESSION['_token_disposable']);
            $session = json_decode($session, true);
            $token = $session[$name] ?? null;

            return is_string($token) ? $token : null;
        }

        /**
         * Authenticate the token.
         * 
         * @param  string  $name
         * @param  string|null  $token
         * @return bool
         */
        public function auth($name, $token = null)
        {
            $throw_error = false;
            $request = request();

            if ($token === null)
                $token = $request->post('_token_disposable');

            if (is_string($token) === true && isset($_SESSION['_token_disposable']) === true) {
                $session = $this->getToken($name);

                if ($session === null || hash_equals($session, $token) === false)
                    $throw_error = true;
            } else
                $throw_error = true;

            if ($throw_error === true) {
                abort(401);

                return false;
            } else {
                $this->delete();

                return true;
            }
        }

        /**
         * Delete the token.
         * 
         * @return bool
         */
        private function delete()
        {
            if (isset($_SESSION['_token_disposable']))
                unset($_SESSION['_token_disposable']);

            return true;
        }

    }