<?php

set_time_limit(0);
$skipOtherTests = 1;
defined('RUN_ALL_TESTS') or require_once '../tests.php';

class TestSmtpNotification extends UnitTestCase
{
    
    CONST API_URL = "http://localhost/LeadwrenchEmail/trunk/email/api/dynect/";
    
    private $_db;
    
    function __construct() {
        $this->_db = new Database;
    }

    public function testDynecPostBack_getBounce_softBounce()
    {
        $url = self::API_URL."getbounce.php";
        $data['email'] = 'test.email.getsoftbounce@gmail.com';
        $data['bouncerule'] = '';
        $data['bouncecode'] = '';
        $data['bouncetype'] = 'soft';
        $data['x1'] = '10000001';

        $this->_sendRequest($url, $data);
   
        $activity = $data['x1'];
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
        
        $teardownCondition['transactions'] = "email = '$email' AND type = 6 AND activity_id = '$activity'";
        $teardownCondition['activity'] = "tracking_id = '$tracking_id'";
        
        $this->_tearDownDb($teardownCondition);
        
        
    }
    //--------------------------------------------------------------------------
    
    public function testDynecPostBack_getBounce_hardBounce()
    {
        $url = self::API_URL."getbounce.php";
        $data['email'] = 'test.email.gethardbounce@gmail.com';
        $data['bouncerule'] = '';
        $data['bouncecode'] = '';
        $data['bouncetype'] = 'hard';
        $data['x1'] = '10000002';

        $this->_sendRequest($url, $data);
        
        $query = "SELECT * FROM suppression_email WHERE email = '$email' AND source = 3 AND reason = 1";
        $result = $this->_db->query($query);
        
        if (empty($result)) {
            $this->assertTrue(false);
        } else {
            $this->assertTrue(true);
        }
        
        $activity = $data['x1'];
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
    
    private function _sendRequest($url, $data, $method = "GET")
    {
        
        if ($method === "GET") {
            $url .= "?";
            $end = "";
            foreach ($data as $field => $value) {
                $url .= $end.$field."=".$value;
            } 
            $data = array();
        }
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