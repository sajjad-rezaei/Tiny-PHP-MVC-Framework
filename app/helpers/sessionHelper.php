<?php

function getSession($key){
    if (session_status() === PHP_SESSION_NONE) 
        session_start();

    return (isset($_SESSION[$key]))? $_SESSION[$key] : false;
        
}
function setSession($key , $value){
    if (session_status() === PHP_SESSION_NONE) 
        session_start();
    $_SESSION[$key] = $value;
}