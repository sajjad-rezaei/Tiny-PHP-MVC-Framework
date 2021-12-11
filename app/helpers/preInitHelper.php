<?php
function handleErrors($errno, $errstr, $errfile, $errline) {

    $logger = new logger(LOG_FILE);    
    $message = "Error : $errno  IN FILE: $errfile ,  LINE: $errline MESSAGE: $errstr\n";
    $type = getErrorType($errno);
    
    $logger->log($message , $type);
    
}

function getErrorType($errno){
    switch ($errno) {
        case E_NOTICE:
        case E_USER_NOTICE:
            return Logger::NOTICE;
            break;
        case E_WARNING:
        case E_USER_WARNING:
            return  Logger::WARNING;
            break;
        case E_ERROR:
        case E_USER_ERROR:
            return Logger::FATAL;
            break;
        default:
            return Logger::ERROR;
            break;
    }
}
function dd(...$data){

    echo"<pre>";
    foreach($data AS $prop){
        var_dump($prop);
        echo PHP_EOL;
    }
    echo"</pre>";
    
    die;
}
