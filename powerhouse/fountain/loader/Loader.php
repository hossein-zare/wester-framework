<?php

namespace Fountain\Loader;

class Loader
{

    /**
     * The list of packages.
     * 
     * @var array
     */
    private $packages = [];

    /**
     * Create a new instance of Loader.
     * 
     * @param  array|string|null  $packages
     */
    public function __construct($packages = null)
    {
        if ($packages === null)
            return;
        
        $this->packages = $this->toArray($packages);
    }

    /**
     * Convert data into array.
     * 
     * @param  string  $data
     * @return array
     */
    private function toArray($data)
    {
        if (is_string($data)) {
            return [$data];
        }

        return $data;
    }

    /**
     * Load packages from a repository.
     * 
     * @param  string  $repository
     * @return Fountain\Loader\Loader::class
     */
    public function load($repository)
    {
        $packages = require $repository;
        return $this->bind($packages);
    }

    /**
     * Bind new packages.
     * 
     * @param  array|string  $packages
     * @return Fountain\Loader\Loader::class
     */
    public function bind($packages)
    {
        $this->packages = array_merge($this->toArray($packages), $packages);

        return $this;
    }

    /**
     * Load the packages.
     * 
     * @param  array|string  $packages
     */
    public function fireUp()
    {
        foreach ($this->packages as $package) {
            require_once $package;
        }
    }

}
