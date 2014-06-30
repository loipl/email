<?php

$skipOtherTests = 1;
defined('RUN_ALL_TESTS') or require_once '../tests.php';

class TestAdNetADKI extends UnitTestCase
{

    public function testADKIUsername()
    {
        $adki = new ADKI;

        $this->assertEqual($adki->getUsername(),Config::$adNetCredentials['adki']['username']);
    }
    //--------------------------------------------------------------------------


    public function testADKIPassword()
    {
        $adki = new ADKI;

        $this->assertEqual($adki->getPassword(),Config::$adNetCredentials['adki']['password']);
    }
    //--------------------------------------------------------------------------


    public function testADKIApiKey()
    {
        $adki = new ADKI;

        $this->assertEqual($adki->getApiKey(),Config::$adNetCredentials['adki']['apikey']);
    }
    //--------------------------------------------------------------------------


    public function testADKIName()
    {
        $adki = new ADKI;

        $this->assertEqual($adki->getName(),'AdKnowledge');
    }
    //--------------------------------------------------------------------------


    public function testADKISend()
    {
        $adki = new ADKI;
        
        $emails = array(
            array(
                'email' => 'dom@edgeprod.com',
                'campaign'  => 'test',
                'country'   =>'US',
                'state' => 'FL',
                'postal_code' => '34614',
                'gender' => 'M',
                'birth_day' => '15',
                'birth_month' => '4',
                'birth_year' => '1979'
            ),
            array(
                'email' => 'dom@leadwrench.com',
                'campaign'  => 'test2',
                'country'   =>'US',
                'state' => 'CT',
                'postal_code' => '06770',
                'gender' => 'M',
                'birth_day' => '30',
                'birth_month' => '4',
                'birth_year' => '1953'
            )
        );

        $leads = Builder::buildLeadData($emails);
        $adkiEmails = $adki->buildPayload($leads);

        $adki->send($adkiEmails, '1');

        $this->assertEqual($adki->getLastStatus(),'success');
    }
    //--------------------------------------------------------------------------
}