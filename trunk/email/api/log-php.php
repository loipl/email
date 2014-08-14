<?php

    require_once dirname(__FILE__) . '/core.php';
    
    if (!isset($requestHandler)) {
        $requestHandler = new RequestHandler('log-php');
    }
    
    $responseHandler = new ResponseHandler();
    $params = $requestHandler->getRequestParams();
    
    $currentPage = !empty($params['page']) ? $params['page'] : '1';
    $fromDate = !empty($params['from_date']) ? $params['from_date'] : date('Y-m-d', time() - 86400);
    $toDate = !empty($params['to_date']) ? $params['to_date'] : date('Y-m-d');
    $searchWord = !empty($params['search_word']) ? $params['search_word'] : "";
    $groupResult = !empty($params['group_result']) ? $params['group_result'] : "";
    
    $requestMethod = $requestHandler->getRequestMethod();
    
    $result = FALSE;
    
    switch ($requestMethod) {
        case 'GET':
            if (isset($params['action']) && $params['action'] === 'count') {
                $countLog = LogPhp::countAll($fromDate, $toDate, $searchWord, $groupResult);
                echo $responseHandler->responseData($countLog);
            } else {
                $allLogs = LogPhp::getAll($fromDate, $toDate, $searchWord, $currentPage, $groupResult);
                echo $responseHandler->responseArray($allLogs);
            }
            break;
        default :
            echo $responseHandler->responseError("Unknown request method");
            break;
    }
    
