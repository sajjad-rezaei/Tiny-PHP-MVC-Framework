<?php

$hook_events = [];
global $hook_events;

//require File Handy For Logging Purpose
require_once "../app/helpers/hooksHelper.php";
//TODO problem with Logger with cant fing hooks
require_once "../app/classes/core/Logger.php";
include_once "../app/helpers/preInitHelper.php";
# include all config files
foreach ( glob("../app/configs/*.php" ) as $filename)  require_once $filename;

# include all helper files
foreach ( glob(APP_PATH . "helpers/*.php" ) as $filename)  include_once $filename;

# include all hooks
foreach ( glob(APP_PATH ."/hooks/*.php" ) as $filename)  include_once $filename;

if(file_exists(APP_PATH. "vendor/autoload.php"))
    require_once APP_PATH. 'vendor/autoload.php';

// Autoload  Classes
spl_autoload_register(function ($className) {

    if (file_exists(APP_PATH. 'classes/' . $className . '.php')) {
        require_once APP_PATH. 'classes/' . $className . '.php';
    }
    elseif (file_exists(APP_PATH. 'classes/core/' . $className . '.php')) {
        require_once APP_PATH. 'classes/core/' . $className . '.php';
    }elseif(class_exists(ucfirst($className)) || class_exists(lcfirst($className))){
        //check if its load in vendor autoload with composer
        return;
    }else
        throw new Exception("Classs {$className} Not Found  to Include!!!");
    

});



$request = new Request();
$logger = new logger(LOG_FILE);

$router = new Router($request->getUrl(OMITT_URL), $request->getMethod());




# include all routes
foreach ( glob(APP_PATH."routes/*.php" ) as $filename)  require_once $filename;


$router->run();




