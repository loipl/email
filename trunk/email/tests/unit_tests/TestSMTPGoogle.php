<?php

$skipOtherTests = 1;
defined('RUN_ALL_TESTS') or require_once '../tests.php';

class TestSMTPGoogle extends UnitTestCase
{

    public function testSMTPGoogle_sendEmail()
    {
        $smtp = new GSMTP;
        
        $fromPerson = "Jason Hart";
        $fromEmail  = Config::$fromDomains[0]['sender'] . "@" . Config::$fromDomains[0]['domain'];
        $res = $smtp->sendEmail(Config::$emailTests, $fromPerson, $fromEmail, 'Email System Test - SMTP','Testing the Email system', false);
        $this->assertEqual($res, true);
    }
    //--------------------------------------------------------------------------
}