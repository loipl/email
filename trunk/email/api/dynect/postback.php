<?php

require_once dirname(__FILE__) . '/../../email.php';

abstract class PostBack {

    private $_db;
    
    function __construct() 
    {
        $this->_db = new Database;
    }
    
    // function to get request from smtp server, should be modify to adapt with whatever they send
    abstract function getRequestParams();

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
                
                if (!empty($record['datetime'])) {
                    $unixTime = strtotime($record['datetime']);
                    
                    if (!empty($unixTime)) {
                        $sql .= '"'.date('Y-m-d H:i:s', $unixTime) .'", ';
                    } else {
                        $sql .= ' NOW(), ';
                    }
                } else {
                    $sql .= ' NOW(), ';
                }
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
        $lead = new Lead($email);
        $sourceCampaign = $lead->getCampaign();
        
        if (intval($activityId) > 0 && ! is_null($email)) {
            // get domain
            $emailDomain = explode('@', $email);
            $domain = '';
            $tldGroup = '';
            
            if (count($emailDomain) > 1) { 
                $domain = $emailDomain[1];
            }
            
            if (!empty($domain)) {
                $tldGroup = TldList::getTldGroupByDomain($domain);
            }

            $throttle = array(
                'type'            => $type,
                'domain'          => $domain,
                'source_campaign' => $sourceCampaign,
                'tld_group'       => $tldGroup,
                'channel'         => Activity::getChannelById($activityId),
                'creative_id'     => Activity::getCreativeIdById($activityId),
                'campaign_id'     => Activity::getCampaignIdById($activityId),
                'category_id'     => Activity::getCategoryIdById($activityId)
            );
            
            // insert throttle if not exist
            if (Throttle::checkThrottleExists($throttle) === FALSE) {
                Throttle::addThrottle($throttle);
            }
        }
    }
    //--------------------------------------------------------------------------
    
    
    // Main function 
    abstract function execute();
}

