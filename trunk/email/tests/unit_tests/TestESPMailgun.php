<?php

set_time_limit(0);
$skipOtherTests = 1;
defined('RUN_ALL_TESTS') or require_once '../tests.php';

class TestESPMailgun extends UnitTestCase
{

    public function testDynect_getUsername()
    {
        $mailgun = new Mailgun;

        $this->assertEqual($mailgun->getUsername(),Config::$espCredentials['mailgun']['username']);
    }
    //--------------------------------------------------------------------------


    public function testDynect_getPassword()
    {
        $mailgun = new Mailgun;

        $this->assertEqual($mailgun->getPassword(),Config::$espCredentials['mailgun']['password']);
    }
    //--------------------------------------------------------------------------


    public function testDynect_getApiKey()
    {
        $mailgun = new Mailgun;

        $this->assertEqual($mailgun->getApiKey(), Config::$espCredentials['mailgun']['apikey']);
    }
    //--------------------------------------------------------------------------


    public function testDynect_getName()
    {
        $mailgun = new Mailgun;

        $this->assertEqual($mailgun->getName(),'Mailgun E-Mail Delivery');
    }
    //--------------------------------------------------------------------------

    public function testGetSentMessages()
    {
        $mailgun = new Mailgun;

        $message = $mailgun->getSentMessages();
        
        $this->assertEqual($mailgun->getLastStatus(),'success');
    }
    //--------------------------------------------------------------------------

    public function testGetUnsubscribeMessages()
    {
        $mailgun = new Mailgun;

        $message = $mailgun->getSentMessages();
        
        $this->assertEqual($mailgun->getLastStatus(),'success');
    }
    //--------------------------------------------------------------------------

    public function testGetSpamMessages()
    {
        $mailgun = new Mailgun;

        $message = $mailgun->getSpamMessages();
        
        $this->assertEqual($mailgun->getLastStatus(),'success');
    }
    //--------------------------------------------------------------------------
    
    public function testGetBounceMessages()
    {
        $mailgun = new Mailgun;

        $message = $mailgun->getBounceMessages();
        
        $this->assertEqual($mailgun->getLastStatus(),'success');
    }
    //--------------------------------------------------------------------------


    public function testSendEmail()
    {
        $api = new Mailgun;
        
        $fromPerson = "Jason Hart";
        $fromEmail  = Config::$fromDomains[0]['sender'] . "@" . Config::$fromDomains[0]['domain'];
        $toEmail = 'diepbuihuu@gmail.com';
        $api->sendEmail($toEmail, $fromPerson, $fromEmail, 'Email System Test - Gunmai', 'Testing the Email system. Sorry if it disturb', '500', '',0, false);
        $this->assertEqual($api->getLastStatus(),'sent');
    }
    //--------------------------------------------------------------------------
}