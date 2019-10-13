<?php

    // Don't touch this code.
    define('CONSOLE', FALSE);

    /**
     * Register classes and load required files.
     */
    require_once('./app/Powerhouse/Bundles/Registrar.php');
    require_once('./app/Powerhouse/Bundles/Loader.php');

    use Database\Cache as DatabaseCache;
    use Powerhouse\Services\WatchTower;

    /**
     * Configure the error handlers.
     */
    WatchTower::registerExceptionHandlers();

    /**
     * Register the shutdown function.
     */
    WatchTower::registerShutdown(function () {
        DatabaseCache::destructAll();
    });

    /**
     * Run the service providers.
     */
    WatchTower::services();

    /**
     * Present the debug info.
     */
    WatchTower::debugInfo();
