<?php

class Auth{

    private $args;
    public static $userId = null;
    public function __construct(...$args){

        //skip splat packing
        $this->args = $args[0];

    }
    public function run(){
        global $logger;
        if(AUTH_TYPE == "JWT" ){
            
            if(JWT_SECURITY_KEY === null );
                //TODO Error;
            $payload = JWTDecode(JWT_SECURITY_KEY , JWT_ALG);
            if(!$payload)
                $logger->log("Auth Error!!" , Logger::FATAL);
            self::$userId =  $payload->id;

        }else{
            
            self::$userId = (getSession('id') AND !empty(getSession('id')))? getSession('id') : false;

        }
        if(!self::$userId) {
            $logger->log("Auth Error!!" , Logger::FATAL);
        }

    }



 


}