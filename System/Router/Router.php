<?php

/**
 *
 * This file is part of mvc-rest-api for PHP.
 *
 */
namespace Router;

use Exception;
use MVC\JWT;

/**
 * Class Router For Handel Router
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
     *  Response Class
     */
    private $response;

    /**
     *  construct
     * @param string $url
     * @param string $method
     */
    public function __construct(string $url, string $method) {
        $this->url = rtrim($url, '/');
        $this->method = $method;

        // get response class of $GLOBALS var
        $this->response = $GLOBALS['response'];
    }

    /**
     *  set get request http method for route
     * @param $pattern
     * @param $callback
     */
    public function get($pattern, $callback) {
        $this->addRoute("GET", $pattern, $callback);
    }

    /**
     *  set post request http method for route
     * @param $pattern
     * @param $callback
     * @param string $rule
     */
    public function post($pattern, $callback, $rule = 'all') {
        $this->addRoute('POST', $pattern, $callback, $rule);
    }

    /**
     *  set put request http method for route
     * @param $pattern
     * @param $callback
     */
    public function put($pattern, $callback) {
        $this->addRoute('PUT', $pattern, $callback);
    }

    /**
     *  set delete request http method for route
     * @param $pattern
     * @param $callback
     */
    public function delete($pattern, $callback) {
        $this->addRoute('DELETE', $pattern, $callback);
    }

    /**
     *  add route object into router var
     * @param $method
     * @param $pattern
     * @param $callback
     * @param string $rule
     */
    public function addRoute($method, $pattern, $callback, string $rule = 'all') {
        array_push($this->router, new Route($method, $pattern, $callback, $rule));
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
     * @param $pattern
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
     * @param $url
     * @param $pattern
     * @return bool
     */
    public function dispatch($url, $pattern) {
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
     * @param $key
     * @param $value
     */
    private function setParams($key, $value) {
        $this->params[$key] = $value;
    }

    /**
     * Convert Pattern To Regex
     * @param $matches
     * @return string
     */
    private function convertPatternToRegex($matches) {
        $key = str_replace(':', '', $matches[0]);
        return '(?P<' . $key . '>[a-zA-Z0-9_\-\.\!\~\*\\\'\(\)\:\@\&\=\$\+,%]+)';
    }

    /**
     *  run application
     * @throws \Exception
     */
    public function run() {
        if (!is_array($this->router) || empty($this->router)) 
            throw new Exception('Путь не найден');

        $this->getMatchRoutersByRequestMethod();
        $this->getMatchRoutersByPattern($this->matchRouter);

        if (!$this->matchRouter || empty($this->matchRouter)) {
			$this->sendNotFound();
		} else {

            $rule = $this->matchRouter[0]->getRule();
            $GLOBALS['params'] = $this->params;
            $request = $GLOBALS['request'];
            $jwt = new JWT();
            $key = $request->getHeader('Authorization');
            if (!isset($key) or empty($key)) {
                $key = $request->input('Authorization');
                if (!isset($key) or empty($key)) {
                    $key = $request->post('Authorization') ? $request->post('Authorization') : $request->post('token');
                } else {
                    throw new \Exception('Подпись не переданна', 401);
                }
            }
            switch ($rule) {
                case 'auth':
                    if ($key !== null && !empty($key)) {
                        if (!$jwt->verify($key)) throw new \Exception('Подпись не верна', 401);
                        $GLOBALS['session'] = $jwt->getData($key);
                    } else {
                        throw new \Exception('Подпись не переданна', 401);
                    }
                    break;
            }

            // call to callback method
            if (is_callable($this->matchRouter[0]->getCallback()))
                call_user_func($this->matchRouter[0]->getCallback(), $this->params);
            else
                $this->runController($this->matchRouter[0]->getCallback(), $this->params);
        }
    }


    /**
     * run as controller
     * @param $controller
     * @param $params
     * @return mixed
     */
    private function runController($controller, $params) {
        $parts = explode('@', $controller);
        $file = CONTROLLERS . ucfirst($parts[0]) . '.php';

        if (file_exists($file)) {
            require_once($file);

            // controller class
            $controller = 'Controllers' . ucfirst($parts[0]);

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
        }
    }
	
	private function sendNotFound() {
		$this->response->setContent(['status' => 'error', 'code' => 404, 'body' => 'Путь не найден'], 'error', 404);
	}
}
