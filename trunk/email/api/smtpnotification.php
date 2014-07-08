<?php

require_once dirname(__FILE__) . '/../email.php';

class SmtpNotification {
    CONST BOUNCE_EVENT_CODE     = 101;
    CONST COMPLAINT_EVENT_CODE  = 102;
    CONST DELIVERY_EVENT_CODE   = 103;
    
    CONST SUPPRESSION_SOURCE    = 8;
    
    private $_db;
    
    function __construct() 
    {
        $this->_db = new Database;
    }
    
    // function to get request from smtp server, should be modify to adapt with whatever they send
    function getRequestParams () 
    {
        $request = array();
        try {
            $request = json_decode($_REQUEST['data']);
        } catch (Exception $ex) {
            $request = array();
        }
        return $request;
    }
    
    // function to add a record into transaction table
    function addTransactions ($record, $type) 
    {
        $activityId = Activity::getActivityIdFromTrackingId($record->tracking_id);
        if (empty($activityId)) {
            $activityId = Activity::addActivity($record->rcpt_to, NULL, NULL, NULL, NULL, NULL, $record->tracking_id);
        }
        // will not add transaction if $type = 0
        if ( isset($record->rcpt_to) && !empty($record->rcpt_to) && $type !== 0 ) {
            if (Transaction::checkTransactionExists($type, $record->rcpt_to, $activityId) === false) {
                $sql  = 'INSERT INTO `transactions` (id, type, email, campaign_id, creative_id, datetime, activity_id) VALUES (';
                $sql .= 'NULL,';
                $sql .= ' \''.$type.'\',';
                $sql .= ' \'' .mysql_real_escape_string($record->rcpt_to). '\',';
                $sql .= ' NULL,';
                $sql .= ' NULL,';
                $sql .= ' NOW(), ';
                $sql .= ' \'' . $activityId . '\' ';
                $sql .= ')';

                $this->_db->query($sql);
            }
        }
    }
    
    // Main function 
    function execute () 
    {
        $data = $this->getRequestParams();

        $mappingRespClassToTransType = array (
            // 0 : no type, 4: softBounce, 6: hardBounce
            '0'   => 0,
            '1'   => 4, // The response text could not be identified. (Undetermined)
            '10'  => 6, // The recipient is invalid. (Invalid Recipient)
            '13'  => 6, // Recipient was internally suppressed. (Suppressed)
            '20'  => 4, // The message expired in queue. (Soft Bounce)
            '21'  => 4, // The message bounced due to a DNS failure. (DNS Failure)
            '22'  => 4, // The message bounced due to the remote mailbox being over quota. (Mailbox Full)
            '23'  => 4, // The message bounced because it was too large for the recipient. (Too Large)
            '24'  => 4, // The message timed out. (Timeout)
            '30'  => 6, // No recipient could be determined for the message. (Generic Bounce: No RCPT)
            '40'  => 4, // The message failed for unspecified reasons. (Generic Bounce)
            '50'  => 4, // The message was blocked by the receiver. (Mail Block)
            '51'  => 4, // The message was blocked by the receiver as coming from a known spam source. (Spam Block)
            '52'  => 4, // The message was blocked by the receiver as spam (Spam Content)
            '53'  => 4, // The message was blocked by the receiver because it contained an attachment (Prohibited Attachment)
            '54'  => 4, // The message was blocked by the receiver because relaying is not allowed. (Relay Denied)
        );
        foreach ($data as $record) {
            $type = 0;
            switch ($record->event_code) {
                // event_code = 101/103 -> Bounce/delivery
                case self::BOUNCE_EVENT_CODE :
                case self::DELIVERY_EVENT_CODE :
                    $type = $mappingRespClassToTransType[$record->resp_class];
                    switch ($record->resp_class) {
                        case 1  : 
                        case 20 :
                        case 21 :
                        case 22 :
                        case 23 :
                        case 24 :
                            Lead::scoreSoftBounce($record->rcpt_to);
                            break;
                        case 10 :
                        case 13 :
                        case 30 :
                        case 40 :
                        case 50 :
                        case 51 :
                        case 52 :
                        case 53 :
                        case 54 :
                            Suppression_Email::addEmailSuppression(mysql_real_escape_string($record->rcpt_to), self::SUPPRESSION_SOURCE, 1);
                            Lead::scoreHardBounce($record->rcpt_to);
                            break;
                    };
                    break;
                
                // event_code = 102 -> complaint
                case self::COMPLAINT_EVENT_CODE :
                    $type = 5;
                    Suppression_Email::addEmailSuppression(mysql_real_escape_string($record->rcpt_to), self::SUPPRESSION_SOURCE, 3);
                    Lead::scoreComplaint($record->rcpt_to);
                    break;
            }
            
            // Add transactions
            $this->addTransactions($record, $type);
        }
    }
}

$smtpNotification = new SmtpNotification ();
$smtpNotification->execute();

