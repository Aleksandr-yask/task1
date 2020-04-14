<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// load config and startup file
require 'config.php';
require SYSTEM . 'Startup.php';
require_once __DIR__ . '/vendor/autoload.php';

// using
use Router\Router;

// create object of request and response class
$request = new Http\Request();
$response = new Http\Response();
$library = new MVC\Library();

$response->setHeader('Access-Control-Allow-Origin: *');
$response->setHeader("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
$response->setHeader('Content-Type: application/json; charset=UTF-8');



// set request url and method
$router = new Router('/' . $request->getUrl(), $request->getMethod());

// import router file
require 'Router/Router.php';
//$router->run();
try {
    // Router Run Request
    $router->run();

} catch (Exception $e) {
    $code = ($e->getCode()) ? $e->getCode() : 500;
    $response->sendStatus($code);

    $response->setContent($e->getMessage(), 'error', $code);
}

// Response Render Content
$response->render();




