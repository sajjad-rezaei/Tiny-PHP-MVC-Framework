<?php



/**
 * Class Router For Handel Router
*          afgprogrammer.com
 * @author Mohammad Rahmani <rto1680@gmail.com>
 *
 * @package Router
 */
class Router {

    /**
     * route list.
     * 
     * @var array
     */
    private $router = [];

    /**
     * match route list.
     * 
     * @var array
     */
    private $matchRouter = [];

    /**
     * request url.
     * 
     * @var string
     */
    private $url;

    /**
     * request http method.
     * 
     * @var string
     */
    private $method;

    /**
     * param list for route pattern
     * 
     * @var array
     */
    private $params = [];

  
    /**
     *  construct
     */
    public function __construct(string $url, string $method) {
        $this->url = rtrim($url, '/');
        $this->method = $method;

    }

    /**
     *  set get request http method for route
     */
    public function get($pattern, $callback , $middleware = []) {

        $this->addRoute("GET", $pattern, $callback , $middleware);
    }

    /**
     *  set post request http method for route
     */
    public function post($pattern, $callback , $middleware = []) {
        $this->addRoute('POST', $pattern, $callback , $middleware);
    }

    /**
     *  set put request http method for route
     */
    public function put($pattern, $callback , $middleware = []) {
        $this->addRoute('PUT', $pattern, $callback , $middleware);
    }

    /**
     *  set delete request http method for route
     */
    public function delete($pattern, $callback , $middleware = []) {
        $this->addRoute('DELETE', $pattern, $callback , $middleware);
    }

    /**
     *  add route object into router var
     */
    public function addRoute($method, $pattern, $callback , $middleware) {
        array_push($this->router, new Route($method, $pattern, $callback , $middleware));
    }

    /**
     *  filter requests by http method
     */
    private function getMatchRoutersByRequestMethod() {
        foreach ($this->router as $value) {
            if (strtoupper($this->method) == $value->getMethod())
                array_push($this->matchRouter, $value);
        }
    }

    /**
     * filter route patterns by url request
     */
    private function getMatchRoutersByPattern($pattern) {
        $this->matchRouter = [];
        foreach ($pattern as $value) {
            if ($this->dispatch(cleanUrl($this->url), $value->getPattern()))
                array_push($this->matchRouter, $value);
        }
    }

    /**
     *  dispatch url and pattern
     */
    public function dispatch($uri, $pattern) {

        $parsUrl = explode('?', $uri);
        $url = $parsUrl[0];

        preg_match_all('@:([\w]+)@', $pattern, $params, PREG_PATTERN_ORDER);

        $patternAsRegex = preg_replace_callback('@:([\w]+)@', [$this, 'convertPatternToRegex'], $pattern);

        if (substr($pattern, -1) === '/' ) {
	        $patternAsRegex = $patternAsRegex . '?';
	    }
        $patternAsRegex = '@^' . $patternAsRegex . '$@';
        
        // check match request url
        if (preg_match($patternAsRegex, $url, $paramsValue)) {
            array_shift($paramsValue);
            foreach ($params[0] as $key => $value) {
                $val = substr($value, 1);
                if ($paramsValue[$val]) {
                    $this->setParams($val, urlencode($paramsValue[$val]));
                }
            }

            return true;
        }

        return false;
    }

    /**
     *  get router
     */
    public function getRouter() {
        return $this->router;
    }

    /**
     * set param
     */
    private function setParams($key, $value) {
        $this->params[$key] = $value;
    }

    /**
     * Convert Pattern To Regex
     */
    private function convertPatternToRegex($matches) {
        $key = str_replace(':', '', $matches[0]);
        return '(?P<' . $key . '>[a-zA-Z0-9_\-\.\!\~\*\\\'\(\)\:\@\&\=\$\+,%]+)';
    }

    /**
     *  run application
     */
    public function run() {
        if (!is_array($this->router) || empty($this->router)) 
            throw new Exception('NON-Object Route Set');

        $this->getMatchRoutersByRequestMethod();
        $this->getMatchRoutersByPattern($this->matchRouter);
        
        if (!$this->matchRouter || empty($this->matchRouter)) {
			$this->sendNotFound();        
		} else {
            

            //call the middlewares before calling controller or call backs
            $this->runMiddleWare();

            // call to callback method
            if (!is_array($this->matchRouter[0]->getCallback()) && is_callable($this->matchRouter[0]->getCallback()))
                call_user_func($this->matchRouter[0]->getCallback(), $this->params);
            else
                $this->runController($this->matchRouter[0]->getCallback(), $this->params);
        }
    }

    /**
     * run as controller
     */
    private function runController($controller, $params) {
        $path = "";
        if(is_array($controller)){
            $path = $controller[1] . "/";
            $controller = $controller[0];
        }
        $parts = explode('@', $controller);
        $file = CONTROLLERS . $path  . ucfirst($parts[0]) . 'Controller.php';
        
        if (file_exists($file)) {
            require_once($file);

            // controller class
            $controller =  ucfirst($parts[0]);
            if (class_exists($controller))
                $controller = new $controller();
            else
				$this->sendNotFound();

            // set function in controller
            if (isset($parts[1])) {
                $method = $parts[1];
				
                if (!method_exists($controller, $method))
                    $this->sendNotFound();
				
            } else {
                $method = 'index';
            }

            // call to controller
            if (is_callable([$controller, $method]))
                return call_user_func([$controller, $method], $params);
            else
				$this->sendNotFound();
        }else
            $this->sendNotFound("File to Handle the Route Path Not Found");
    }
    private function runMiddleware(){

        $finalMiddleWaresList = [];
        if(USE_MIDDLEWARES_FOR_ROUTES !== null AND !empty(USE_MIDDLEWARES_FOR_ROUTES))
                $finalMiddleWaresList = USE_MIDDLEWARES_FOR_ROUTES;

        if(!empty($this->matchRouter[0]->getMiddleware()) AND is_array($this->matchRouter[0]->getMiddleware())){

            foreach($this->matchRouter[0]->getMiddleware() AS $middleware){
                $middleWareName = (($middleware[0] == '-' OR $middleware[0] == '+')? substr($middleware, 1) : $middleware); 
                
                switch($middleware[0]){
                    case '-':
                        $pos = array_search($middleWareName , $finalMiddleWaresList);
                        
                        if($pos !== false)
                            unset($finalMiddleWaresList[$pos]);
                        break;
                    case '+':
                    default:
                        $pos = array_search($middleWareName , $finalMiddleWaresList);
                        if($pos === false)
                            array_push($finalMiddleWaresList , $middleware);
                        break;
                }
                
            }

        }

        foreach($finalMiddleWaresList AS $middleware){

            

            $file = MIDDLEWARES   . ucfirst($middleware) . '.php';
            
            if (file_exists($file)) 
                require_once($file);
            else
                $this->sendNotFound("File to Handle the MiddleWare  Not Found");
                
            // controller class
            $middleware =  ucfirst($middleware);
            if (class_exists($middleware)){
                $middleware = new $middleware($this->params);
                $this->params = $middleware->run();
            }
            else
                $this->sendNotFound();
        }


    }
	
	private function sendNotFound($message = "") {
        global $logger;
        $message = (empty($message))? "Not Found From Router" : $message;
        $logger->log($message);
		return notFoundResponse($message);
	}
}
