<?php

# renders request response to the output buffer (ref: zend-diactoros)
function render($body, $code = 200, $headers = []) {
    
    http_response_code($code);
    array_walk($headers, function ($value, $key) {
        if (! preg_match('/^[a-zA-Z0-9\'`#$%&*+.^_|~!-]+$/', $key)) {
            throw new InvalidArgumentException("Invalid header name - {$key}");
        }
        $values = is_array($value) ? $value : [$value];
        foreach ($values as $val) {
            if (
        preg_match("#(?:(?:(?<!\r)\n)|(?:\r(?!\n))|(?:\r\n(?![ \t])))#", $val) ||
        preg_match('/[^\x09\x0a\x0d\x20-\x7E\x80-\xFE]/', $val)
      ) {
                throw new InvalidArgumentException("Invalid header value - {$val}");
            }
        }
        header($key . ': ' . implode(',', $values));
    });
    
    print $body;
}



# creates standard json response
function jsonResponse($body, $code = 200, array $headers = ['content-type' => 'application/json']) {
    render(json_encode($body), $code, $headers);
    die();
}
# creates standard response
function response($body, $code = 200, array $headers = []) {
    render($body, $code, $headers);
    die();
}
#notFound
function notFoundResponse($message){
    $message = (empty($message))? "Not Found" : $message;
    jsonResponse(["success" => false , "message" => $message] , 404 );
}
# creates redirect response
function redirect($location, $code = 302) {
    http_response_code($code);
    header("Location: " . BASE_URL  . $location);
}

#get header
function getHeader($key){
    $headers = apache_request_headers();
    return (isset($headers[$key]))? $headers[$key] : false;
}

function clean($data) {
    return trim(htmlspecialchars($data, ENT_COMPAT, 'UTF-8'));
}

function cleanUrl($url) {
    return str_replace(['%20', ' '], '-', $url);
}