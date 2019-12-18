<?php

use Powerhouse\Routing\Router;

Router::get('/a/{id:[a-zA-Z]+}', 'Action->run')->namespace('ssss')->pattern(['id' => 'ssss']);
