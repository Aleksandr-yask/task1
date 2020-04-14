<?php

/**
 * Отвечает за вывод ошибок в json
 *
 * @param $severity
 * @param $message
 * @param $file
 * @param $line
 * @throws ErrorException
 */
//function exception_error_handler($severity, $message, $file, $line) {
//    if (!(error_reporting() & $severity)) {
//        return;
//    }
//    throw new ErrorException($message, 0, $severity, $file, $line);
//}
//set_error_handler("exception_error_handler");


// Http Url
$scriptName = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
define('HTTP_URL', '/'. substr_replace(trim($_SERVER['REQUEST_URI'], '/'), '', 0, strlen($scriptName)));

// Define Path Application
define('SCRIPT', str_replace('\\', '/', rtrim(__DIR__, '/')) . '/');
define('SYSTEM', SCRIPT . 'System/');
define('CONTROLLERS', SCRIPT . 'Application/Controllers/');
define('MODELS', SCRIPT . 'Application/Models/');
define('UPLOAD', SCRIPT . 'Upload/');
define('SECRET', 'b3b82e18022ef1abbfebd8a8c535182a9aaffcd363a7e870d3cb70fe2dab25bf');

// DB_PREFIX
define('DB_PREFIX', '');
