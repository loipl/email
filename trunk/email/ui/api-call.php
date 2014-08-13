<?php

require_once dirname(__FILE__) . '/../email.php';

// -----------------------------------------------------------------------------
function getAllThrottle($apiBase, $params) 
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
function countAllThrottle($apiBase, $params) 
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
function getAllDebugLog($apiBase, $params) 
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
function countAllDebugLog($apiBase, $params) 
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
function getAllSchedulerLog($apiBase, $params) 
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
function countAllSchedulerLog($apiBase, $params) 
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