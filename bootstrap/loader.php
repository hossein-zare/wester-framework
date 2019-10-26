<?php

use Fountain\Loader\Loader;
use Fountain\Loader\Config;

// Get the configuration array
new Config();

// Load required file packages
$loader = new Loader();

// Load packages from the repository
$loader->load(__DIR__ . '/../config/packages.php');

// Fire up
$loader->fireUp();

// Unset the loader
unset($loader);
