<?php

    require_once dirname(__FILE__) . '/core.php';
    
    if (!isset($requestHandler)) {
        $requestHandler = new RequestHandler('log-debug');
    }
    
    $responseHandler = new ResponseHandler();
    $params = $requestHandler->getRequestParams();
    
    $currentPage = !empty($params['page']) ? $params['page'] : '1';
    $fromDate = !empty($params['from_date']) ? $params['from_date'] : date('Y-m-d', time() - 86400);
    $toDate = !empty($params['to_date']) ? $params['to_date'] : date('Y-m-d');
    $searchWord = !empty($params['search_word']) ? $params['search_word'] : "";
    
    $requestMethod = $requestHandler->getRequestMethod();
    
    $result = FALSE;
    
    switch ($requestMethod) {
        case 'GET':
            if (isset($params['action']) && $params['action'] === 'count') {
                $countLog = LogDebug::countAll($fromDate, $toDate, $searchWord);
                echo $responseHandler->responseData($countLog);
            } else {
                $allLogs = LogDebug::getAll($fromDate, $toDate,$searchWord, $currentPage);
                echo $responseHandler->responseArray($allLogs);
            }
            break;
        default :
            echo $responseHandler->responseError("Unknown request method");
            break;
    }
    

?>
