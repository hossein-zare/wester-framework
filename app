#!/usr/bin/env php
<?php

    // Don't touch this code.
    define('CONSOLE', TRUE);

    /**
     * Register classes and load required files.
     */
    require_once('./public/app/Powerhouse/Bundles/Registrar.php');
    require_once('./public/app/Powerhouse/Bundles/Loader.php');

    use Powerhouse\Console\ConsoleApplication;

    /**
     * Create a console application.
     */
    $console = new ConsoleApplication();

    /**
     * Run the application.
     */
    $console->serve($argc, $argv);

    /**
     * Shut down the console.
     */
    $console->shutdown();
