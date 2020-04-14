<?php

/**
 *
 * This file is part of mvc-rest-api for PHP.
 *
 */
namespace MVC;

use Exception;

/**
 * Class Controller, a port of MVC
 *
 * @package MVC
 */
class Controller {

    public $lib;
    public $fKey;
    public $session;
    public $run=false;
    public $model;
    public $sqlMsgLimit = 20;
    public $data;
    public $prms;

    /**
     * Request Class.
     */
    public $request;

    /**
     * Response Class.
     */
    public $response;

    /**
     * Sessions class
     */
    public $sessions;

	/**
	*  Construct
	*/
    public function __construct() {
        $this->request = $GLOBALS['request'];
        $this->response = $GLOBALS['response'];
        $this->prms = $GLOBALS['params'];

        if (isset($GLOBALS['session']))
            $this->session = $GLOBALS['session'];

        $this->data = ($this->request->input()) ? $this->request->input() : $this->request->post();
//        if (!is_array($this->data) or count($this->data) === 0) {
//            // неверный формат запроса/запрос пустой
//            throw new Exception('Неверный формат запроса или запрос пустой', 400);
//        }
        $this->lib = new Library();


    }

    /**
     * get Model
     *
     * @param string $model
     *
     * @return object
     * @throws Exception
     */
    public function model($model) {
        $file = MODELS . ucfirst($model) . '.php';

		// check exists file
        if (file_exists($file)) {
            require_once $file;

            $model = 'Models' . str_replace('/', '', ucwords($model, '/'));
			// check class exists
            if (class_exists($model))
                return new $model;
            else 
                throw new Exception(sprintf('{ %s } this model class not found', $model));
        } else {
            throw new Exception(sprintf('{ %s } this model file not found', $file));
        }
    }


	// send response faster
    public function send(string $msg, int $status = 200) {
        $this->response->setHeader(sprintf('HTTP/1.1 ' . $status . ' %s' , $this->response->getStatusCodeText($status)));
        $this->response->setContent($msg);
    }
}
