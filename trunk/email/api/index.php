<?php

require_once dirname(__FILE__) . '/core.php';

$url = preg_replace('/\?.*$/', '', $_SERVER['REQUEST_URI']);
$urlComponents = explode('/', $url);

$apiName = '';



foreach ($urlComponents as $index => $component) {
    if ($component === 'api') {
        $apiName = $urlComponents[$index + 1];
        $requestHandler = new RequestHandler($apiName);
        if (!empty($urlComponents[$index + 2])) {
            $requestHandler->addParams('id', $urlComponents[$index + 2]);
        }
    }
}

if (file_exists($apiName . '.php')) {
    require_once $apiName . '.php';
} else {
    echo 'fail';
}

