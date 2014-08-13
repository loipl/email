<?php

    require_once dirname(__FILE__) . '/core.php';
    
    if (!isset($requestHandler)) {
        $requestHandler = new RequestHandler('throttle');
    }
    
    $responseHandler = new ResponseHandler();
    $params = $requestHandler->getRequestParams();
    
    $sortBy = !empty($params['sort_by']) ? $params['sort_by'] : 'id';
    $sortOrder = !empty($params['sort_order']) ? $params['sort_order'] : 'DESC';
    $currentPage = !empty($params['page']) ? $params['page'] : '1';
    $fromDate = !empty($params['from_date']) ? $params['from_date'] : date('Y-m-d', time() - 86400);
    $toDate = !empty($params['to_date']) ? $params['to_date'] : date('Y-m-d');
    
    $requestMethod = $requestHandler->getRequestMethod();
    
    $result = FALSE;
    
    switch ($requestMethod) {
        case 'GET':
            if (isset($params['action']) && $params['action'] === 'count') {
                $countLog = Throttle::countAll($fromDate, $toDate);
                echo $responseHandler->responseData($countLog);
            } else {
                $allLogs = Throttle::getAll($fromDate, $toDate, $currentPage, $sortBy, $sortOrder);
                echo $responseHandler->responseArray($allLogs);
            }
            break;
        default :
            echo $responseHandler->responseError("Unknown request method");
            break;
    }
