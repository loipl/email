<?php

class ResponseHandler {

    const STATUS_SUCCESS = '1';
    const STATUS_FAILURE = '0';
    
    public function responseArray($array) {
        $result = array(
            'status' => self::STATUS_SUCCESS,
            'count'  => count($array),
            'data'   => $array
        );

        return json_encode($result);
    }
    // -------------------------------------------------------------------------
    
    public function responseData($data) {
        $result = array(
            'status' => self::STATUS_SUCCESS,
            'data'   => $data
        );

        return json_encode($result);
    }
    // -------------------------------------------------------------------------
    public function responseError($message) {
        $result = array(
            'status' => self::STATUS_FAILURE,
            'message'  => $message
        );

        return json_encode($result);
    }
    // -------------------------------------------------------------------------
    
    public function responseSuccess($message) {
        $result = array(
            'status' => self::STATUS_SUCCESS,
            'message'  => $message
        );

        return json_encode($result);
    }
    // -------------------------------------------------------------------------

}
