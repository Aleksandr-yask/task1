<?php

/**
 *
 * This file is part of mvc-rest-api for PHP.
 *
 */
namespace Router;

/**
 * Class Route For Save Route
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
     *  Allows these HTTP methods.
     *
     *  @var array
     */
    private $list_method = ['GET', 'POST', 'PUT', 'DELETE', 'OPTION'];

    /**
     * Rule
     *
     * @var string
     */
    private $rule;

    /**
     *  construct function
     * @param String $method
     * @param String $pattern
     * @param $callback
     * @param string $rule
     */
    public function __construct(String $method, String $pattern, $callback, string $rule) {
        $this->method = $this->validateMethod(strtoupper($method));
        $this->pattern = cleanUrl($pattern);
        $this->callback = $callback;
        $this->rule = $rule;
    }

    /**
     *  check valid method
     * @param string $method
     * @return string
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

    public function getRule(): string
    {
        return $this->rule;
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
}
