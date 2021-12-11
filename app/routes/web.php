<?php


$router->get('/home', 'samples@index', ["-auth"]);
//use - or + for auth
$router->get('/home/:id/:name', 'samples@index' , ["-auth"]);

// If you use SPACE in the url, it should convert the space to -, /home-index
$router->get('/home index', 'samples@index');


$router->post('/home', 'samples@post' );

$router->get('/home/subfolder', ['samples@index' , "subfolder"]);

$router->get('/', function() {
    echo 'Welcome ';
});