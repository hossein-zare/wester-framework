<?php

    /**
     * Here we register namespaces for classes.
     * 
     * @var array
     */
    CONST CLASS_ALIASES = [
        '^Powerhouse\\' => 'App\\Powerhouse\\Framework\\',
        '^Packages\\' => 'App\\Powerhouse\\Packages\\',
        '^Requests\\' => 'App\\Transit\\Http\Requests\\',
        '^Models\\' => 'App\\Transit\\Models\\',
        '^Mails\\' => 'App\\Transit\\Mails\\',
        '^AppBundles\\' => 'App\\Transit\\AppBundles\\',
        
        // Register your package if you don't want to access it with the prefix AppBundles
        '^Carbon\\' => 'App\\Transit\\AppBundles\\Carbon\\src\\Carbon\\',
        '^Cactus\\' => 'App\\Powerhouse\\Framework\\Database\\Cactus\\',
        '^Database\\' => 'App\\Powerhouse\\Framework\\Database\\',
    ];
