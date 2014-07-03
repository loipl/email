<?php

require_once dirname(__FILE__) . '/../../email.php';

abstract class PostBack {

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
    //--------------------------------------------------------------------------
    
    
    protected function addThrottles($type, $activityId) 
    {
        $activityId = mysql_real_escape_string($activityId);
        $email = Activity::getEmailById($activityId);
        
        if (intval($activityId) > 0 && ! is_null($email)) {
            // get domain
            $emailDomain = explode('@', $email);
            if (count($emailDomain) > 1) { 
                $domain = $emailDomain[1];
            } else {
                $domain = '';
            }

            $throttle = array(
                'type'          => $type,
                'domain'        => $domain,
                'channel'       => Activity::getChannelById($activityId),
                'creative_id'   => Activity::getCreativeIdById($activityId),
                'campaign_id'   => Activity::getCampaignIdById($activityId),
                'category_id'   => Activity::getCategoryIdById($activityId)
            );
            
            // insert throttle if not exist
            if (Throttle::checkThrottleExists($throttle) === FALSE) {
                Throttle::addThrottle($throttle);
            }
        }
    }
    //--------------------------------------------------------------------------
    
    
    // Main function 
    abstract function execute ();
}

