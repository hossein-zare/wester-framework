<?php

    namespace Powerhouse\Foundation\Middleware;

    use Powerhouse\Routing\Route;
    use Powerhouse\Castles\Root;
    
    abstract class CsrfProtection
    {
        /**
         * Handle The CSRF Token.
         * 
         * @param  object  $request
         * @return bool
         */
        final public function handle($request)
        {
            // Exceptions
            if (Root::arrBranchOf(Route::$uri, $this->except) === true)
                return true;

            $throw_error = false;
            $method = $request->method();
            $real_method = $request->realMethod();
            $isAPI = Route::getRoutingType() === 'api';

            if (in_array($method, ['GET', 'HEAD']) === false && $isAPI === false) {
                $token = $request->ajax() ? $request->response()->header('HTTP-X-CSRF-TOKEN') : $request->retrieveByMethod($real_method, '_token');

                if (is_string($token) === true) {
                    $session = session()->get('_token');

                    if (is_string($session) === false || hash_equals($session, $token) !== true)
                        $throw_error = true;
                } else
                    $throw_error = true;
            }

            if ($throw_error === true) {
                abort(401);

                return false;
            } else
                return true;
        }
        
    }
