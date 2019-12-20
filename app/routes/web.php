<?php

use Powerhouse\Routing\Router;

Router::get('/page/{id}', 'Welcome->index')->pattern(['id' => '[0-9]+']);
