<?php

//if project is in subfolder we should ommit the subfolder form requestURI for Routing
// start with  / => /project
define("OMITT_URL" , "/");

// Http Url
$scriptName = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
define('HTTP_URL', '/'. substr_replace(trim($_SERVER['REQUEST_URI'], '/'), '', 0, strlen($scriptName)));
// Define Path Application
define('PUBLIC_PATH', str_replace('\\', '/', rtrim(dirname(__DIR__ , 1), '/')) . '/../public/');
define('APP_PATH', str_replace('\\', '/', rtrim(dirname(__DIR__ , 1), '/')) . '/');


define("BASE_URL" , "http://mysite.com");

define('CONTROLLERS', APP_PATH . 'controllers/');
define('MODELS', APP_PATH . 'models/');
define('VIEWS', APP_PATH . 'views/');
define('MIDDLEWARES', APP_PATH . 'middlewares/');
define('ASSETS', APP_PATH . 'Upload/');

define("ERROR_DEFAULT_MESSAGE" , "there was an error");




