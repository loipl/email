<?php

    require_once dirname(__FILE__) . '/core.php';
    
    if (!isset($requestHandler)) {
        $requestHandler = new RequestHandler('creative');
    }
    
    $responseHandler = new ResponseHandler();
    $params = $requestHandler->getRequestParams();
    
    $requestMethod = $requestHandler->getRequestMethod();
    
    $result = FALSE;
    
    switch ($requestMethod) {
        case 'GET':
            $result = Creative::getAllCreatives();
            echo $responseHandler->responseArray($result);
            break;
        case 'POST':
            if ($params['action'] === 'update') {
                $id = $params['id'];
                if (empty($id) || !is_numeric($id)) {
                    echo $responseHandler->responseError('Invalid creative id');
                    return;
                }
                Creative::updateCreativeById($id, $params);
                echo $responseHandler->responseSuccess("Success");
            } elseif ($params['action'] === 'add') {
                Creative::addCreative($params);
                echo $responseHandler->responseSuccess("Success");
            } else {
                echo $responseHandler->responseError('Unknown action');
            }
            
            break;
        case 'DELETE':
            $id = $params['id'];
            if (empty($id) || !is_numeric($id)) {
                echo $responseHandler->responseError('Invalid campaign id');
                return;
            }
            
            Creative::deleteCreative($id);
            echo $responseHandler->responseSuccess("Success");
            break;
        default :
            echo $responseHandler->responseError("Unknown request method");
            break;
    }
    

?>
