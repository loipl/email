<?php

set_time_limit(0);
$skipOtherTests = 1;
defined('RUN_ALL_TESTS') or require_once '../tests.php';

class TestSmtpNotification extends UnitTestCase
{
    
    CONST API_URL = "http://localhost/LeadwrenchEmail/trunk/email/api/smtpnotification.php";
    
    private $_db;
    
    function __construct() {
        $this->_db = new Database;
    }

    public function testSMTPNotification_DeliveryAndHardBounceResponse()
    {
        $url = self::API_URL;
        $email = "test.email@gmail.com";
        $tracking_id = "6d267641-aa1f-49ac-8952-testtracking";
        $data['data'] = '[{"mail_from": "foo@bar.com",
            "rcpt_to": "' . $email . '",
            "time_started": 1306924062,
            "time_finished": 1306924065,
            "status": 2,
            "resp_code": 13,
            "resp_msg": "OK",
            "resp_class": 13,
            "tries": 1,
            "tracking_id": "' . $tracking_id . '",
            "event_code": 103,
            "event_label": "delivery"}]';

        $this->_sendRequest($url, $data);
        
        $query = "SELECT * FROM suppression_email WHERE email = '$email' AND source = 8 AND reason = 1";
        $result = $this->_db->query($query);
        
        if (empty($result)) {
            $this->assertTrue(false);
        } else {
            $this->assertTrue(true);
        }
        
        $activity = Activity::getActivityIdFromTrackingId($tracking_id);
        if (empty($activity)) {
            $this->assertTrue(false);
        } else {
            $fresult = Transaction::checkTransactionExists(6, $email, $activity);
            if ($fresult) {
                $this->assertTrue(true);
            } else {
                $this->assertTrue(false);
            }
        }
        
        $teardownCondition['transactions'] = "email = '$email' AND type = 6 AND activity_id = '$activity'";
        $teardownCondition['supression_email'] = "email = '$email' AND source = 8 AND reason = 1";
        $teardownCondition['activity'] = "tracking_id = '$tracking_id'";
        
        $this->_tearDownDb($teardownCondition);
        
        
    }
    //--------------------------------------------------------------------------
    
    public function testSMTPNotification_SoftBounceResponse()
    {
        $url = self::API_URL;
        $email = "test.email.softbounce@gmail.com";
        $tracking_id = "6d267641-aa1f-49ac-8952-testsoftbounce";
        $data['data'] = '[{"mail_from": "foo@bar.com",
            "rcpt_to": "' . $email . '",
            "time_started": 1306924062,
            "time_finished": 1306924065,
            "status": 2,
            "resp_code": 20,
            "resp_msg": "OK",
            "resp_class": 20,
            "tries": 1,
            "tracking_id": "' . $tracking_id . '",
            "event_code": 101,
            "event_label": "bounce"}]';

        $this->_sendRequest($url, $data);

        $activity = Activity::getActivityIdFromTrackingId($tracking_id);
        if (empty($activity)) {
            $this->assertTrue(false);
        } else {
            $fresult = Transaction::checkTransactionExists(4, $email, $activity);
            if ($fresult) {
                $this->assertTrue(true);
            } else {
                $this->assertTrue(false);
            }
        }
        
        $teardownCondition['transactions'] = "email = '$email' AND type = 4 AND activity_id = '$activity'";
        $teardownCondition['activity'] = "tracking_id = '$tracking_id'";
        
        $this->_tearDownDb($teardownCondition);
    }
    //--------------------------------------------------------------------------
    
    public function testSMTPNotification_ComplaintResponse()
    {
        $url = self::API_URL;
        $email = "test.email.complaint@gmail.com";
        $tracking_id = "6d267641-aa1f-49ac-8952-testcomplaint";
        $data['data'] = '[{
            "feedback_type": "spam",
            "subject": "ExAmPlE.CoM NeWsLeTtEr!!!",
            "time_received": 1306924062,
            "rcpt_to": "' . $email . '",
            "mail_from": "newsletter@smtp.com",
            "tracking_id": "' . $tracking_id . '",
            "time_sent": 1304246829,
            "sender_id": 1000001,
            "resolution": 2,
            "event_code": 102,
            "event_label": "complaint"}]';

        $this->_sendRequest($url, $data);

        $query = "SELECT * FROM suppression_email WHERE email = '$email' AND source = 8 AND reason = 3";
        $result = $this->_db->query($query);
        if (empty($result)) {
            $this->assertTrue(false);
        } else {
            $this->assertTrue(true);
        }
              
        $activity = Activity::getActivityIdFromTrackingId($tracking_id);
        if (empty($activity)) {
            $this->assertTrue(false);
        } else {
            $fresult = Transaction::checkTransactionExists(5, $email, $activity);
            if ($fresult) {
                $this->assertTrue(true);
            } else {
                $this->assertTrue(false);
            }
        }
        
        $teardownCondition['transactions'] = "email = '$email' AND type = 5 AND activity_id = '$activity'";
        $teardownCondition['supression_email'] = "email = '$email' AND source = 8 AND reason = 3";
        $teardownCondition['activity'] = "tracking_id = '$tracking_id'";
        
        $this->_tearDownDb($teardownCondition);
    }
    //--------------------------------------------------------------------------
    
    private function _sendRequest($url, $data, $method = "POST")
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $rs = curl_exec($ch);
        curl_close($ch);
        echo "respone : ". $rs."\n";
        return $rs;
    }
    //--------------------------------------------------------------------------
    
    private function _tearDownDb($teardownData)
    {
        foreach ($teardownData as $table => $condition) {
            $query = "DELETE FROM $table WHERE $condition";
            echo $query."\n";
            $this->_db->query($query);
        }
    }
    //--------------------------------------------------------------------------
    
}