<?php

    require_once dirname(__FILE__) . '/core.php';
    
    $requestHandler = new RequestHandler('campaign');
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
            break;
    }
    

?>
