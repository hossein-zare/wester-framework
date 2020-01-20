<?php

    if (CONSOLE === FALSE)
        $loader_prefix = './app';
    else
        $loader_prefix = './public/app';
        
    require_once("{$loader_prefix}/Wester.php");
    require_once("{$loader_prefix}/Config/Web.php");
    require_once("{$loader_prefix}/Config/Database.php");
    require_once("{$loader_prefix}/Config/FileSystems.php");
    require_once("{$loader_prefix}/Config/Cache.php");
    require_once("{$loader_prefix}/Config/Mail.php");
    require_once("{$loader_prefix}/Config/Cookie.php");
    require_once("{$loader_prefix}/Config/Session.php");
    require_once("{$loader_prefix}/Config/Hashing.php");
    require_once("{$loader_prefix}/Powerhouse/Framework/Support/Helper.php");