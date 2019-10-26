<?php

use Fountain\Repository\Helper;
use Fountain\Loader\Config;

if (! function_exists('toObject')) {
    /**
     * Convert value into an object.
     * 
     * @param  mixed  $data
     * @return object
     */
    function toObject($value)
    {
        if (! is_array($value))
            return $value;

        return json_decode(json_encode($value));
    }
}

if (! function_exists('config')) {
    /**
     * Get the configuration.
     * 
     * @param  string  $section
     * @return Fountain\Loader\Config
     */
    function config($section = null)
    {
        if ($section !== null)
            return (Helper::get(Config::class))->$section();
        return Helper::get(Config::class);
    }
}
