<?php
function post($key){

    return (isset($_POST[$key]))? $_POST[$key] : false;
}
function get($key){

    return (isset($_GET[$key]))? $_GET[$key] : false;
}
function getUrl($path){
    $path = ltrim($path , "/");
    return BASE_URL . "/" . $path;
}