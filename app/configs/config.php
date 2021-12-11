<?php
//change the timezone for your country
date_default_timezone_set('Asia/Tehran');

ini_set("log_errors", 1);
ini_set("error_log", "./logs/.php-error.log");
set_error_handler("handleErrors");
//log path
define("LOG_FILE" , "./logs/.logs.log");

//all routes use this middlewares
//can be override in defining the route
define('USE_MIDDLEWARES_FOR_ROUTES', [
    "auth"
]);

// Config Database
define('DATABASE', [
    'Host'   => 'localhost',
    'Name'   => '###',
    'User'   => '###',
    'Pass'   => '###',
    "Type"   => 'mysql'
]);

define("MEDOO_OBJ" , 1);
define("MEDOO_ARRAY" , 2);
//config for select mode to return Object or not for each select in the project
//can be override in select function in model
define("MEDOO_SELECT_TYPE" , MEDOO_OBJ);

define("DEBUG_MODE" , true);
//dont pay attention to this errors and dont print them strictly
define("EXCLUDE_CATCH_ERRORS" , [Logger::NOTICE , Logger::WARNING]);

// JWT
//if AUTH_TYPE = session  then Auth middleware use $_session['id']
define("AUTH_TYPE" , "JWT");
define('JWT_SECURITY_KEY', "JWT_KEY");
define('JWT_ALG', "HS512");

