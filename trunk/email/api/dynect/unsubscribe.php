<?php

require_once dirname(__FILE__) . '/../../email.php';
require_once dirname(__FILE__) . '/postback.php';

class Unsubscribe extends PostBack {
    
    function __construct() 
    {
        parent::__construct();
    }
    
    // function to get request from smtp server, should be modify to adapt with whatever they send
    function getRequestParams()
    {
        $request = array();
        if(isset($_GET['email'])) {
            $request['email'] = mysql_real_escape_string($_GET['email']);
        } else {
            return array();
        }
        
        if (isset($_GET['unsubscribetime'])) {
            $request['datetime'] = $_GET['unsubscribetime'];
        }
        
        $request['X-Activity-ID'] = (isset($_GET['x1'])) ? mysql_real_escape_string($_GET['x1']) : '';
        
        return $request;
    }
    
    // Main function 
    function execute()
    {
        $request = $this->getRequestParams();
        
        if (empty($request)) {
            return;
        }

        $type = Config::TRANSACTION_TYPE_UNSUB;
        Lead::scoreUnsubscribe($request['email']);
        Suppression_Email::addEmailSuppression(mysql_real_escape_string($request['email']), Config::SUPPRESSION_SOURCE, Config::SUPRESS_REASON_UNSUB);
        // Add transactions
        $this->addTransactions($request, $type);
        
    }
}

$dynectGetUnsub = new Unsubscribe();
$dynectGetUnsub->execute();