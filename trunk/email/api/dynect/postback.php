<?php

require_once dirname(__FILE__) . '/../../email.php';

abstract class PostBack {
    CONST TRANSACTION_TYPE_OPEN         = 1;    // open
    CONST TRANSACTION_TYPE_CLICK        = 2;    // click
    CONST TRANSACTION_TYPE_UNSUB        = 3;    // unsubcribe
    CONST TRANSACTION_TYPE_SOFTBOUNCE   = 4;    // soft bounce
    CONST TRANSACTION_TYPE_COMPLAINT    = 5;    // complaint
    CONST TRANSACTION_TYPE_HARDBOUNCE   = 6;    // hardbounce
    CONST TRANSACTION_TYPE_CONVERSION   = 10;   // conversion
    
    CONST SUPRESS_REASON_HARDBOUNCE     = 1;    // hard bounce
    CONST SUPRESS_REASON_PREVHARDBOUNCE = 2;    // previously hard bounce
    CONST SUPRESS_REASON_COMPLAINT      = 3;    // complaint
    CONST SUPRESS_REASON_PREVCOMPLAINT  = 4;    // previously complaint
    CONST SUPRESS_REASON_IMPORTED       = 5;    // imported data
    CONST SUPRESS_REASON_UNSUB          = 6;    // unsubcribe
    CONST SUPRESS_REASON_HYGIENEFAIL    = 7;    // hygiene fail
    
    CONST SUPPRESSION_SOURCE    = 3;

    private $_db;
    
    function __construct() 
    {
        $this->_db = new Database;
    }
    
    // function to get request from smtp server, should be modify to adapt with whatever they send
    abstract function getRequestParams ();

    // function to add a record into transaction table
    protected function addTransactions ($record, $type) 
    {
        $activityId = mysql_real_escape_string($record['X-Activity-ID']);
        // will not add transaction if $type = 0
        if ( isset($record['email']) && !empty($record['email']) && $type !== 0 && $activityId > 0 ) {
            if (Transaction::checkTransactionExists($type, $record['email'], $activityId) === false) {
                $sql  = 'INSERT INTO `transactions` (id, type, email, campaign_id, creative_id, datetime, activity_id) VALUES (';
                $sql .= 'NULL,';
                $sql .= ' \''.$type.'\',';
                $sql .= ' \'' .mysql_real_escape_string($record['email']). '\',';
                $sql .= ' NULL,';
                $sql .= ' NULL,';
                $sql .= ' NOW(), ';
                $sql .= ' \'' . $activityId . '\' ';
                $sql .= ')';

                $this->_db->query($sql);
            }
        }
    }
    
    
    protected function addThrottles($type, $activityId) 
    {
        $activityId = mysql_real_escape_string($activityId);
        
        if (intval($activityId) > 0 && ! is_null(Activity::getEmailById($activityId))) {
            $throttle = array(
                'type'          => $type,
                'domain'        => 'domain',
                'channel'       => Activity::getChannelById($activityId),
                'creative_id'   => Activity::getCreativeIdById($activityId),
                'campaign_id'   => Activity::getCampaignIdById($activityId),
                'category_id'   => Activity::getCategoryIdById($activityId)
            );
            
            if (Throttle::checkThrottleExists($throttle) === FALSE) {
                Throttle::addThrottle($throttle);
            }
        }
    }
    //--------------------------------------------------------------------------
    
    
    // Main function 
    abstract function execute ();
}

