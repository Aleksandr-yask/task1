<?php

/**
 *
 * This file is part of mvc-rest-api for PHP.
 *
 */
namespace MVC;

use Database\DatabaseAdapter;

class Model
{

    public $db;

    public function __construct(){
        $this->db = new DatabaseAdapter;
    }
}
