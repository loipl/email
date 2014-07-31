<?php

class CurlHelper {
    public function __construct() {
        ;
    }
    
    public static function request($url, $method = 'GET', $params = array()) {
        $options = array(
            CURLOPT_URL				=> $url,
            CURLOPT_CUSTOMREQUEST   => $method,
            CURLOPT_FOLLOWLOCATION	=> true,
            CURLOPT_AUTOREFERER		=> true,
            CURLOPT_RETURNTRANSFER	=> true,
            CURLOPT_TIMEOUT			=> 60,
            CURLOPT_POSTFIELDS      => $params
        );
        
        $ch = curl_init();
        curl_setopt_array($ch, $options);
        $content    = curl_exec($ch);
        $httpCode   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err        = curl_error($ch);
        $errno      = curl_errno($ch);
        curl_close($ch);

        return array(
            'httpCode'  => $httpCode,
            'httpErr'   => $err,
            'httpErrno' => $errno,
            'content'   => $content
        );
    }
}

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
