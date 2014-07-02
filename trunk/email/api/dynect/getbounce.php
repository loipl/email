<?php

require_once dirname(__FILE__) . '/../../email.php';
require_once dirname(__FILE__) . '/postback.php';

class GetBounce extends PostBack {
    CONST SOFT_BOUNCE           = 'soft';
    CONST HARD_BOUNCE           = 'hard';
    CONST PREV_HARD_BOUNCE      = 'previouslyhardbounced';
    
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
        
        $request['bouncerule']      = (isset($_GET['bouncerule'])   ?   mysql_real_escape_string($_GET['bouncerule'])   : '');
        $request['bouncetype']      = (isset($_GET['bouncetype'])   ?   mysql_real_escape_string($_GET['bouncetype'])   : '');
        $request['bouncecode']      = (isset($_GET['bouncecode'])   ?   mysql_real_escape_string($_GET['bouncecode'])   : '');
        $request['X-Activity-ID']   = (isset($_GET['x1'])           ?   mysql_real_escape_string($_GET['x1'])           : '');
        
        return $request;
    }
    
    // Main function 
    function execute () 
    {
        $request = $this->getRequestParams();
        
        if (empty($request)) {
            return;
        }

        $mappingRespClassToTransType = array (
            // 0 : no type, 4: softBounce, 6: hardBounce
            ''                          => 0,
            'soft'                      => self::TRANSACTION_TYPE_SOFTBOUNCE, // softbounce
            'hard'                      => self::TRANSACTION_TYPE_HARDBOUNCE, // hardbounce
            'previouslyhardbounced'     => self::TRANSACTION_TYPE_HARDBOUNCE  // hardbounce
        );
        
        $type = 0;
        $type = $mappingRespClassToTransType[$request['bouncetype']];
        $activityId = $request['X-Activity-ID'];
        
        switch ($request['bouncetype']) {
            // case soft bounce
            case self::SOFT_BOUNCE :
                Lead::scoreSoftBounce($request['email']);
                
                // Add transactions
                $this->addTransactions($request, $type);
                
                // add data to throttles table
                $this->addThrottles($type, $activityId);
                break;
            
            // case hard bounce
            case self::HARD_BOUNCE :
                Suppression_Email::addEmailSuppression(mysql_real_escape_string($request['email']), self::SUPPRESSION_SOURCE, self::SUPRESS_REASON_HARDBOUNCE);
                Lead::scoreHardBounce($request['email']);
                
                // Add transactions
                $this->addTransactions($request, $type);
                
                // add data to throttles table
                $this->addThrottles($type, $activityId);
                break;
            
            // case previously hard bounce
            case self::PREV_HARD_BOUNCE :
                Suppression_Email::addEmailSuppression(mysql_real_escape_string($request['email']), self::SUPPRESSION_SOURCE, self::SUPRESS_REASON_PREVHARDBOUNCE);
                Lead::scoreHardBounce($request['email']);
                break;
            
            default :
                return;
        }
        
    }
}

$dynectGetBounce = new GetBounce ();
$dynectGetBounce->execute();

