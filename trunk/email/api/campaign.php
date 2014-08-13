<?php

    require_once dirname(__FILE__) . '/core.php';
    
    if (!isset($requestHandler)) {
        $requestHandler = new RequestHandler('campaign');
    }
    
    $responseHandler = new ResponseHandler();
    $params = $requestHandler->getRequestParams();
    
    $requestMethod = $requestHandler->getRequestMethod();
    
    $result = FALSE;
    
    switch ($requestMethod) {
        case 'GET':
            $campaigns = Campaign::getAllCampaign();
            $result = Campaign::formatCampaign($campaigns);
            echo $responseHandler->responseArray($result);
            break;
        case 'POST':
            if ($params['action'] !== 'add') {
                $id = $params['id'];
                if (empty($id) || !is_numeric($id)) {
                    echo $responseHandler->responseError('Invalid campaign id');
                    return;
                }
            }
            
            if ($params['action'] === 'update') {
                Campaign::updateCampaignById($id, $params);
                echo $responseHandler->responseSuccess("Success");
            } else if ($params['action'] === 'add') {
                Campaign::insertCampaign($params['name'], $params['attributes'], $params['send_limit'], $params['send_count'], $params['creative_ids'], $params['end_date']);
                echo $responseHandler->responseSuccess("Success");
            } else if ($params['action'] === 'copy') {
                Campaign::copyCampaign($id);
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
            
            Campaign::deleteCampaign($id);
            echo $responseHandler->responseSuccess("Success");
            break;
        default :
            echo $responseHandler->responseError("Unknown request method");
            break;
    }
    
