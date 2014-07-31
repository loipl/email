<?php

class RequestHandler {
    private $name;
    
    public function __construct($name) {
        $this->name = $name;
    }
    // -------------------------------------------------------------------------
    
    public function getRequestParams() {
        $result = array();
        $result += $_GET;
        $result += $_POST;
        return $result;
    }
    // -------------------------------------------------------------------------
    
    public function getRequestMethod() {
        return $_SERVER['REQUEST_METHOD'];
    }
    // -------------------------------------------------------------------------
}

?>
