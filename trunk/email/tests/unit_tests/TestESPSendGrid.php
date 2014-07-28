<?php

$skipOtherTests = 1;
defined('RUN_ALL_TESTS') or require_once '../tests.php';

class TestESPSendGrid extends UnitTestCase
{

    public function testSendgrid_getUsername()
    {
        $sendgrid = new Sendgrid;

        $this->assertEqual($sendgrid->getUsername(),Config::$espCredentials['sendgrid']['username']);
    }
    //--------------------------------------------------------------------------


    public function testSendgrid_getPassword()
    {
        $sendgrid = new Sendgrid;

        $this->assertEqual($sendgrid->getPassword(),Config::$espCredentials['sendgrid']['password']);
    }
    //--------------------------------------------------------------------------


    public function testSendgrid_getApiKey()
    {
        $sendgrid = new Sendgrid;

        $this->assertEqual($sendgrid->getApiKey(),Config::$espCredentials['sendgrid']['apikey']);
    }
    //--------------------------------------------------------------------------


    public function testSendgrid_getName()
    {
        $sendgrid = new Sendgrid;

        $this->assertEqual($sendgrid->getName(),'SendGrid');
    }
    //--------------------------------------------------------------------------


    public function testSendgrid_sendEmail()
    {
        $api = new Sendgrid;
        
        $fromPerson = "Jason Hart";
        $fromEmail  = Config::$fromDomains[0]['sender'] . "@" . Config::$fromDomains[0]['domain'];
        $api->sendEmail(Config::$emailTests, $fromPerson, $fromEmail, 'Email System Test - SendGrid', 'Testing the Email system', '500', '',0, false);
        $this->assertEqual($api->getLastStatus(),'sent');
    }
    //--------------------------------------------------------------------------


    public function testSendgrid_getBlocks()
    {
        $api = new Sendgrid;
        
        $api->getBlocks("2013-05-01", "2013-06-01");
        
        $this->assertEqual($api->getLastStatus(), 'success');
    }
    //--------------------------------------------------------------------------


    public function testSendgrid_getBounces()
    {
        $api = new Sendgrid;
        
        $api->getBounces("2013-05-01", "2013-06-01");
        
        $this->assertEqual($api->getLastStatus(), 'success');
    }
    //--------------------------------------------------------------------------


    public function testSendgrid_getInvalidList()
    {
        $api = new Sendgrid;
        
        $api->getInvalidList("2013-05-01", "2013-06-01");
        
        $this->assertEqual($api->getLastStatus(), 'success');
    }
    //--------------------------------------------------------------------------


    public function testSendgrid_getSpamList()
    {
        $api = new Sendgrid;
        
        $api->getSpamList("2013-05-01", "2013-06-01");
        
        $this->assertEqual($api->getLastStatus(), 'success');
    }
    //--------------------------------------------------------------------------


    public function testSendgrid_getUnsubscribes()
    {
        $api = new Sendgrid;
        
        $api->getUnsubscribes("2013-05-01", "2013-06-01");
        
        $this->assertEqual($api->getLastStatus(), 'success');
    }
    //--------------------------------------------------------------------------


    public function testSendgrid_getStatistics()
    {
        $api = new Sendgrid;
        
        $api->getStatistics("2013-05-01", "2013-06-01");
        
        $this->assertEqual($api->getLastStatus(), 'success');
    }
    //--------------------------------------------------------------------------
}