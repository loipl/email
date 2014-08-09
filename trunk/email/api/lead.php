<?php

require_once dirname(__FILE__) . '/core.php';

const MYSQL_ERROR_DUPLICATE_KEY = 1062;

$xmlData = file_get_contents('php://input');

if (!isset($requestHandler)) {
    $requestHandler = new RequestHandler('lead');
}

$params = $requestHandler->getRequestParams();

$requestMethod = $requestHandler->getRequestMethod();

if ($xmlData) {
    $xml = new SimpleXMLElement($xmlData);

    if (!filter_var($xml->lead->email[0], FILTER_VALIDATE_EMAIL)) {
        die("LEAD REJECTED: The email address provided ({$xml->lead->email[0]}) is invalid.");
    }
    
    $leadData = Lead::getLeadDataFromXml($xml);
    // if method = post and action = update, change behavior to update instead of insert
    if ($requestMethod === 'POST' && isset($params['action']) && $params['action'] === 'update') {
        $requestMethod = 'PUT';
    }

    if ($requestMethod === 'POST') {
        
        $result = Lead::addRecord($leadData);
        
        if ($result === true) {
            echo "LEAD ACCEPTED";
            return;
        }

    } else if ($requestMethod === 'PUT') {

        $result = Lead::updateRecord($leadData);
        
         if ($result === true) {
            echo "LEAD UPDATED";
            return;
        }
        
    }
    
    if (is_array($result)) {
        if ($result['error_number'] == MYSQL_ERROR_DUPLICATE_KEY) {
            echo "LEAD REJECTED: Duplicate";
        } else {
            echo "LEAD REJECTED: " . $result['error'];
        }
    } else {
        echo "No data or email is missing";
    }
    
} else if ($requestMethod === 'GET') {
    if ($_GET['id']) {
         if (!filter_var($_GET['id'], FILTER_VALIDATE_EMAIL)) {
            die("LEAD REJECTED: The email address provided is invalid.");
        }

        $lead = new Lead($_GET['id']);
        echo Lead::formatLead($lead);
    } 
} else if ($requestMethod === 'DELETE') {
    if ($_GET['id']) {
        $result = Lead::deleteRecord($_GET['id']);
        if ($result === true) {
            echo "LEAD DELETED";
        } else {
            echo "Error";
        }
    }
}
