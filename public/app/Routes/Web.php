<?php

    use App\Transit\Http\Handler\Request;
    use Powerhouse\Castles\Auth;

    // Welcome page
    $route->view('/', 'welcome')->name('home');

    // Authentication routes
    Auth::setRoutes($route);
