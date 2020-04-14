<?php
/**
 * Параметр rule
 * отвечает за права доступа
 * Есть 2 значение: auth - Только с токеном
 *                  all - Любой (токен не проверяется, с сессией работы не идет), стандартное значение
 */

$router->post('/contacts', 'contacts@addContacts');
$router->get('/contacts/:phone', 'contacts@getContacts');


// If you use SPACE in the url, it should convert the space to -, /home-index
//
// $routerpostget('/home', 'home@index');
//
//$routerpostget('/home index', 'home@index');
//
//$router->post('/upload', 'home@uploadImage');
//
//$router->post('/home', 'home@post');
//
//
//$routerpostget('/:name', function($param) {
//    echo 'Welcome '. $param['name'];
//});
//
//$routerpostget('/', function() {
//    echo 'Welcome ';
//});