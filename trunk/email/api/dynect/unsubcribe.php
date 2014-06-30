<?php

require_once dirname(__FILE__) . '/../../email.php';
require_once dirname(__FILE__) . '/postback.php';

class Unsubcribe extends PostBack {
    
    function __construct() 
    {
        parent::__construct();
    }
    
    // function to get request from smtp server, should be modify to adapt with whatever they send
    function getRequestParams () 
    {
        $request = array();
        if(isset($_GET['email'])) {
            $request['email'] = mysql_escape_string($_GET['email']);
        } else {
            return array();
        }
        
        $request['X-Activity-ID']   = (isset($_GET['x1'])   ?   mysql_real_escape_string($_GET['x1'])   : '');
        
        return $request;
    }
    
    // Main function 
    function execute () 
    {
        $request = $this->getRequestParams();
        
        if (empty($request)) {
            return;
        }
        
        $type = self::TRANSACTION_TYPE_UNSUB;
        Lead::scoreUnsubscribe($request['email']);
        Suppression_Email::addEmailSuppression(mysql_real_escape_string($request['email']), self::SUPPRESSION_SOURCE, self::SUPRESS_REASON_UNSUB);
        // Add transactions
        $this->addTransactions($request, $type);
        
    }
}

$dynectGetUnsub = new Unsubcribe();
$dynectGetUnsub->execute();

