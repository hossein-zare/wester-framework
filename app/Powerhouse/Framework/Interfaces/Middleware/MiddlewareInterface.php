<?php

    namespace Powerhouse\Interfaces\Middleware;

    interface MiddlewareInterface
    {
        public function handle(App\Transit\Http\Request $request);
    }
