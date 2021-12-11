<?php

add_hook("logger_hook" , function($message , $messageType){
   // echo ("hook with callable <br>");
    //echo "hook send us this message !!! {$message} With Message Type : {$messageType} <br>";
}); 

function hookWithName($message , $messageType){
    //echo ("hook with string name <br>");
    if($messageType !== Logger::WARNING){
        //echo "hook send us this message !!! {$message} With Message Type : {$messageType} <br>";
    }
}
add_hook("logger_hook" ,"hookWithName"); 