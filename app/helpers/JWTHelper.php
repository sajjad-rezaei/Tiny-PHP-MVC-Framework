<?php

 /**
     * Get hearder Authorization
     * */
    function getAuthorizationHeader(){
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        }
        else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        return $headers;
    }
    /**
     * get access token from header
     * */
    function getBearerToken() {
        global $logger;
        $headers = getAuthorizationHeader();
        // HEADER: Get the access token from the header
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
        }
        $logger->log("Authorization Header Not Found!" , $logger::FATAL);

    }


    function JWTToken($payload , $secretKey){
        global $logger;
        try{
            //for autoload
            new JWT;
            return JWT::encode($payload , $secretKey);
        }catch (Exception $exception){
            $logger->log("Authorization Failed!" , $logger::FATAL);
        }


    }

    /*
     * Decode JWT Token
     */
    function JWTDecode($secretKey , $alg){
        global $logger;
        try{
            //for autoload
            new JWT;
            $token = getBearerToken();

            $payload = JWT::decode($token , $secretKey, [$alg]);
            if($payload === false){
                $logger->log("Authorization Header Not Found!" , $logger::FATAL);
            }
            return $payload;

        }catch (Exception $exception){
            $logger->log("Authorization Failed!" , $logger::FATAL);
        }
    }
    