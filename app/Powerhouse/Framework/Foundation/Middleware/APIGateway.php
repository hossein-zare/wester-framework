<?php
    namespace Powerhouse\Foundation\Middleware;

    use Powerhouse\Castles\Response;
    use Models\AuthSession;

    abstract class APIGateway
    {

        /**
         * Handle HTTP Requests,
         *
         * @param  object  $request
         * @return bool
         */
        final public function handle($request)
        {
            $api_token = Response::header('HTTP-X-API-TOKEN') ?? $request->post('api_token') ?? $request->input('api_token');

            if ($api_token !== null) {
                $result = AuthSession::authenticateToken($api_token);
                if ($result === true)
                    return true;
            }

            return false;
        }

    }
