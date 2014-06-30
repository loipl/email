<?php

set_time_limit(0);
$skipOtherTests = 1;
defined('RUN_ALL_TESTS') or require_once '../tests.php';

class TestESPSMTPCom extends UnitTestCase
{

    public function testSMTPCom_getUsername()
    {
        $smtpCom = new SmtpCom;

        $this->assertEqual($smtpCom->getUsername(),Config::$espCredentials['smtpcom']['username']);
    }
    //--------------------------------------------------------------------------


    public function testSMTPCom_getPassword()
    {
        $smtpCom = new SmtpCom;

        $this->assertEqual($smtpCom->getPassword(),Config::$espCredentials['smtpcom']['password']);
    }
    //--------------------------------------------------------------------------


    public function testSMTPCom_getApiKey()
    {
        $smtpCom = new SmtpCom;

        $this->assertEqual($smtpCom->getApiKey(), Config::$espCredentials['smtpcom']['apikey']);
    }
    //--------------------------------------------------------------------------

    public function testSMTPCom_getName()
    {
        $smtpCom = new SmtpCom;

        $this->assertEqual($smtpCom->getName(),'Smtp.com E-Mail Delivery');
    }
    //--------------------------------------------------------------------------

    public function testSMTPCom_getRestUrl()
    {
        $smtpCom = new SmtpCom;

        $this->assertEqual($smtpCom->getRestUrl(),'http://api.smtp.com/v1/');
    }
    //--------------------------------------------------------------------------

    public function testSMTPCom_getPort()
    {
        $smtpCom = new SmtpCom;

        $this->assertEqual($smtpCom->getPort(),Config::$espCredentials['smtpcom']['port']);
    }
    //--------------------------------------------------------------------------
    
     public function testSMTPCom_getHost()
    {
        $smtpCom = new SmtpCom;

        $this->assertEqual($smtpCom->getHost(),Config::$espCredentials['smtpcom']['host']);
    }
    //--------------------------------------------------------------------------
    
    public function testSMTPCom_sendEmail()
    {
        $api = new SmtpCom;
        
        $fromPerson = "Jason Hart";
        $fromEmail  = Config::$fromDomains[0]['sender'] . "@" . Config::$fromDomains[0]['domain'];
        $api->sendEmail(Config::$emailTests, $fromPerson, $fromEmail, 'Email System Test - Smtp.com', 'Testing the Email system', '500', '', false);
        $this->assertEqual($api->getLastStatus(),'sent');
    }
    //--------------------------------------------------------------------------

}