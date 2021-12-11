<?php


/**
 * Class Route For Save Route
 *
 * @author Mohammad Rahmani <rto1680@gmail.com>
 *
 * @package Router
 */
final class Route {
    
    /**
     *  Http Method.
     * 
     * @var string 
     */
    private $method;

    /**
     *  The path for this route.
     * 
     *  @var string 
     */
    private $pattern;

    /**
     * The action, controller, callable. this route points to.
     * 
     * @var mixed
     */
    private $callback;

    /**
     * The array of middlewares
     * 
     * @var mixed
     */
    private $middleware;

    /**
     *  Allows these HTTP methods.
     *
     *  @var array
     */
    private $list_method = ['GET', 'POST', 'PUT', 'DELETE', 'OPTION'];

    /**
     *  construct function
     */
    public function __construct(String $method, String $pattern, $callback , $middleware) {
        $this->method = $this->validateMethod(strtoupper($method));
        $this->pattern = cleanUrl($pattern);
        $this->callback = $callback;
        $this->middleware = $middleware;
    }

    /**
     *  check valid method
     */
    private function validateMethod(string $method) {
        if (in_array(strtoupper($method), $this->list_method)) 
            return $method;
        
        throw new Exception('Invalid Method Name');
    }

    /**
     *  get method
     */
    public function getMethod() {
        return $this->method;
    }

    /**
     *  get pattern
     */
    public function getPattern() {
        return $this->pattern;
    }

    /**
     *  get callback
     */
    public function getCallback() {
        return $this->callback;
    }
    /**
     *  get middleware
     */
    public function getMiddleware() {
        return $this->middleware;
    }
}
