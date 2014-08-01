<?php

class RequestHandler {
    private $_name;
    
    private $_params;
    
    public function __construct($name) {
        $this->_name = $name;
        $this->_params = array();
    }
    // -------------------------------------------------------------------------
    
    public function addParams($key, $value) {

        $this->_params[$key] = $value;
    }
    // -------------------------------------------------------------------------
    
    public function getRequestParams() {
        $result = $this->_params;
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
