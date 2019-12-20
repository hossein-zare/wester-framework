<?php

use Powerhouse\Routing\Router;

Router::get('/a/{id}', function ($request, $id) {
    echo '\''.$request->name.'\'';
})->pattern(['id' => '^[0-9]+$']);
