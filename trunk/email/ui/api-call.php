<?php

require_once dirname(__FILE__) . '/../email.php';


function getAllRecords($apiBase, $params) 
{
    $apiUrl = $apiBase . '?' . http_build_query($params);
    $apiResponse = CurlHelper::request($apiUrl);

    if ($apiResponse['httpCode'] === 200) {
        $content = json_decode($apiResponse['content'], true);
        return $content['data']; 
    } else {
        return array();
    }
}
// -----------------------------------------------------------------------------


function countAllRecords($apiBase, $params) 
{
    $params['action'] = 'count';
    $apiUrl = $apiBase . '?' . http_build_query($params);
    $apiResponse = CurlHelper::request($apiUrl);
    
    if ($apiResponse['httpCode'] === 200) {
        $content = json_decode($apiResponse['content'], true);
        return $content['data']; 
    } else {
        return 0;
    }
}
// -----------------------------------------------------------------------------