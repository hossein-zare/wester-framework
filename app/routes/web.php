<?php

use Powerhouse\Routing\Router;

Router::get('/a/{id}', 'Welcome->index')->pattern(['id' => '[0-9]+']);
