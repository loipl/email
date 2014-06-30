<?php

set_time_limit(0);
$skipOtherTests = 1;
defined('RUN_ALL_TESTS') or require_once '../tests.php';

class TestESPDynect extends UnitTestCase
{

    public function testDynect_getUsername()
    {
        $dynect = new Dynect;

        $this->assertEqual($dynect->getUsername(),Config::$espCredentials['dynect']['username']);
    }
    //--------------------------------------------------------------------------


    public function testDynect_getPassword()
    {
        $dynect = new Dynect;

        $this->assertEqual($dynect->getPassword(),Config::$espCredentials['dynect']['password']);
    }
    //--------------------------------------------------------------------------


    public function testDynect_getApiKey()
    {
        $dynect = new Dynect;

        $this->assertEqual($dynect->getApiKey(), Config::$espCredentials['dynect']['apikey']);
    }
    //--------------------------------------------------------------------------


    public function testDynect_getName()
    {
        $dynect = new Dynect;

        $this->assertEqual($dynect->getName(),'Dynect E-Mail Delivery');
    }
    //--------------------------------------------------------------------------


    public function testDynect_sendEmail()
    {
        $api = new Dynect;
        
        $fromPerson = "Jason Hart";
        $fromEmail  = Config::$fromDomains[0]['sender'] . "@" . Config::$fromDomains[0]['domain'];
        $api->sendEmail(Config::$emailTests, $fromPerson, $fromEmail, 'Email System Test - Dynect', 'Testing the Email system', '500', '', false);
        $this->assertEqual($api->getLastStatus(),'sent');
    }
    //--------------------------------------------------------------------------


    public function testDynect_getSuppressionList()
    {
        $startTime = '2013-09-02 06:00:00';
        $endTime = '2013-09-02 06:02:00';
        $suppressionsUrl = 'http://emailapi.dynect.net/rest/json/suppressions?apikey=' . Config::$espCredentials['dynect']['apikey']
            . '&noheaders=1&starttime=' . urlencode($startTime) . '&endtime=' . urlencode($endTime);

        $error = 'Invalid response from the suppressions URL';
        $response = $this->_getUrlData($suppressionsUrl);
        $responseObject = json_decode($response);
        $this->assertNotNull($responseObject, $error);
        $this->assertNotEqual('', $responseObject, $error);

        $expectedSuppressions = $responseObject->response->data->suppressions;
        $this->assertNotNull($expectedSuppressions, $error);

        $api = new Dynect;
        $suppressions = $api->getSuppressionList($startTime, $endTime);

        $this->assertEqual($api->getLastStatus(), 'success');

        $error = 'getSuppressionList does not work properly';
        $expectedSuppressionsCount = count($expectedSuppressions);
        $suppressionsCount = count($suppressions);
        $this->assertEqual($expectedSuppressionsCount, $suppressionsCount, $error);
        for ($i = 0; $i < $expectedSuppressionsCount; $i++) {
            $thisError = "[$i] $error";
            $expected = $expectedSuppressions[$i];
            $actual = $suppressions[$i];
            $this->assertEqual($expected->emailaddress, $actual->emailaddress);
            $this->assertEqual($expected->reasontype, $actual->reasontype);
            $this->assertEqual($expected->suppresstime, $actual->suppresstime);
        }
    }
    //--------------------------------------------------------------------------


    public function testDynect_getSuppressionStatus()
    {
        $emailaddress = Config::$emailTests;
        $suppressionStatusUrl = 'http://emailapi.dynect.net/rest/json/recipients/status?apikey=' . Config::$espCredentials['dynect']['apikey']
            . '&emailaddress=' . urlencode($emailaddress);
            
        $error = 'Invalid response from the suppression status URL';
        $response = $this->_getUrlData($suppressionStatusUrl);
        $responseObject = json_decode($response);
        $this->assertNotNull($responseObject, $error);
        $this->assertNotEqual('', $responseObject, $error);
        
        $expected = $responseObject->response->data->recipients;
        $this->assertNotNull($expected, $error);
        $this->assertEqual(1, count($expected), $error);
        
        $api = new Dynect;
        $actual = $api->getSuppressionStatus($emailaddress);
        
        $this->assertEqual($api->getLastStatus(), 'success');
        $this->assertEqual($expected[0]->suppressionstatus, $actual);
    }
    //--------------------------------------------------------------------------


    public function testDynect_getReportSuccesses()
    {
        $startTime = '2013-09-02 06:00:00';
        $endTime = '2013-09-02 06:00:15';
        $successesUrl = 'http://emailapi.dynect.net/rest/json/reports/delivered?apikey=' . Config::$espCredentials['dynect']['apikey']
            . '&noheaders=1&starttime=' . urlencode($startTime) . '&endtime=' . urlencode($endTime);
        
        $error = 'Invalid response from the successes URL';
        $response = $this->_getUrlData($successesUrl);
        $responseObject = json_decode($response);
        $this->assertNotNull($responseObject, $error);
        $this->assertNotEqual('', $responseObject, $error);
        
        $expectedSuccesses = $responseObject->response->data->delivered;
        $this->assertNotNull($expectedSuccesses, $error);
        
        $api = new Dynect;
        $successes = $api->getReportSuccesses($startTime, $endTime);
        
        $this->assertEqual($api->getLastStatus(), 'success');
        
        $error = 'getReportSuccesses does not work properly.';
        $expectedSuccessesCount = count($expectedSuccesses);
        $successesCount = count($successes);
        $this->assertEqual($expectedSuccessesCount, $successesCount, $error);
        for ($i = 0; $i < $expectedSuccessesCount; $i++) {
            $thisError = "[$i] $error";
            $expected = $expectedSuccesses[$i];
            $actual = $successes[$i];
            $this->assertEqual($expected->userid, $actual->userid, $thisError);
            $this->assertEqual($expected->senttime, $actual->senttime, $thisError);
            $this->assertEqual($expected->mssenttime, $actual->mssenttime, $thisError);
            $this->assertEqual($expected->emailaddress, $actual->emailaddress, $thisError);
            $this->assertEqual($expected->xheaders->{'X-CONTENT-ID'}, $actual->xheaders->{'X-CONTENT-ID'}, $thisError);
        }
    }
    //--------------------------------------------------------------------------


    public function testDynect_getReportProblems()
    {
        $startTime = '2009-01-01 00:00:00';
        $endTime = '2013-09-10 12:00:00';
        $problemsUrl = 'http://emailapi.dynect.net/rest/json/reports/issues?apikey=' . Config::$espCredentials['dynect']['apikey']
            . '&noheaders=1&starttime=' . urlencode($startTime) . '&endtime=' . urlencode($endTime);
        
        $error = 'Invalid response from the problems URL';
        $response = $this->_getUrlData($problemsUrl);
        $responseObject = json_decode($response);
        $this->assertNotNull($responseObject, $error);
        $this->assertNotEqual('', $responseObject, $error);
        
        $expectedProblems = $responseObject->response->data->issue;
        $this->assertNotNull($expectedProblems, $error);
        
        $api = new Dynect;
        $problems =  $api->getReportProblems($startTime, $endTime);
        
        $this->assertEqual($api->getLastStatus(), 'success');
        
        $error = 'getReportProblems does not work properly.';
        $expectedProblemsCount = count($expectedProblems);
        $problemsCount = count($problems);
        $this->assertEqual($expectedProblemsCount, $problemsCount, $error);
    }
    //--------------------------------------------------------------------------
    

    public function testDynect_getHardBounces()
    {
        $type = 'hard';
        $startTime = '2013-09-02 06:00:00';
        $endTime = '2013-09-02 06:05:00';
        $hardBouncesUrl = 'http://emailapi.dynect.net/rest/json/reports/bounces?apikey=' . Config::$espCredentials['dynect']['apikey']
            . '&bouncetype=' . $type . '&noheaders=1&starttime=' . urlencode($startTime)
            . '&endtime=' . urlencode($endTime);
        
        $error = 'Invalid response from the hard bounces URL';
        $response = $this->_getUrlData($hardBouncesUrl);
        $responseObject = json_decode($response);
        $this->assertNotNull($responseObject, $error);
        $this->assertNotEqual('', $responseObject, $error);
        
        $expectedBounces = $responseObject->response->data->bounces;
        $this->assertNotNull($expectedBounces, $error);
        
        $api = new Dynect;
        $bounces = $api->getBounceList($startTime, $endTime, $type);
        
        $this->assertEqual($api->getLastStatus(), 'success');
        
        $error = 'getBounceList does not work properly.';
        $expectedBouncesCount = count($expectedBounces);
        $bouncesCount = count($bounces);
        $this->assertEqual($expectedBouncesCount, $bouncesCount, $error);
        for ($i = 0; $i < $expectedBouncesCount; $i++) {
            $thisError = "[$i] $error";
            $expected = $expectedBounces[$i];
            $actual = $bounces[$i];
            $this->assertEqual($expected->emailaddress, $actual->emailaddress, $thisError);
            $this->assertEqual($expected->bouncetype, $actual->bouncetype, $thisError);
            $this->assertEqual($expected->bouncerule, $actual->bouncerule, $thisError);
            $this->assertEqual($expected->bouncecode, $actual->bouncecode, $thisError);
            $this->assertEqual($expected->bouncetime, $actual->bouncetime, $thisError);
            $this->assertEqual($expected->notifiedtime, $actual->notifiedtime, $thisError);
            $this->assertEqual($expected->notified, $actual->notified, $thisError);
            $this->assertEqual($expected->xheaders->{'X-CONTENT-ID'}, $actual->xheaders->{'X-CONTENT-ID'}, $thisError);
        }
    }
    //--------------------------------------------------------------------------
    

    public function testDynect_getPreviouslyHardBounces()
    {
        $type = 'previouslyhardbounced';
        $startTime = '2013-09-02 07:00:00';
        $endTime = '2013-09-02 07:05:00';
        $prevHardBouncesUrl = 'http://emailapi.dynect.net/rest/json/reports/bounces?apikey=' . Config::$espCredentials['dynect']['apikey']
            . '&bouncetype=' . $type . '&noheaders=1&starttime=' . urlencode($startTime)
            . '&endtime=' . urlencode($endTime);
        
        $error = 'Invalid response from the previously hard bounces URL';
        $response = $this->_getUrlData($prevHardBouncesUrl);
        $responseObject = json_decode($response);
        $this->assertNotNull($responseObject, $error);
        $this->assertNotEqual('', $responseObject, $error);
        
        $expectedBounces = $responseObject->response->data->bounces;
        $this->assertNotNull($expectedBounces, $error);
        
        $api = new Dynect;
        $bounces = $api->getBounceList($startTime, $endTime, 'previouslyhardbounced');
        
        $this->assertEqual($api->getLastStatus(), 'success');
        
        $error = 'getBounceList does not work properly.';
        $expectedBouncesCount = count($expectedBounces);
        $bouncesCount = count($bounces);
        $this->assertEqual($expectedBouncesCount, $bouncesCount, $error);
        for ($i = 0; $i < $expectedBouncesCount; $i++) {
            $thisError = "[$i] $error";
            $expected = $expectedBounces[$i];
            $actual = $bounces[$i];
            $this->assertEqual($expected->emailaddress, $actual->emailaddress, $thisError);
            $this->assertEqual($expected->bouncetype, $actual->bouncetype, $thisError);
            $this->assertEqual($expected->bouncerule, $actual->bouncerule, $thisError);
            $this->assertEqual($expected->bouncecode, $actual->bouncecode, $thisError);
            $this->assertEqual($expected->bouncetime, $actual->bouncetime, $thisError);
            $this->assertEqual($expected->notifiedtime, $actual->notifiedtime, $thisError);
            $this->assertEqual($expected->notified, $actual->notified, $thisError);
            $this->assertEqual($expected->xheaders->{'X-CONTENT-ID'}, $actual->xheaders->{'X-CONTENT-ID'}, $thisError);
        }
    }
    //--------------------------------------------------------------------------
    

    public function testDynect_getPreviouslyComplained()
    {
        $type = 'previouslycomplained';
        $startTime = '2013-08-20 07:00:00';
        $endTime = '2013-08-21 07:05:00';
        $hardBouncesUrl = 'http://emailapi.dynect.net/rest/json/reports/bounces?apikey=' . Config::$espCredentials['dynect']['apikey']
            . '&bouncetype=' . $type . '&noheaders=1&starttime=' . urlencode($startTime)
            . '&endtime=' . urlencode($endTime);
        
        $error = 'Invalid response from the previously complained URL';
        $response = $this->_getUrlData($hardBouncesUrl);
        $responseObject = json_decode($response);
        $this->assertNotNull($responseObject, $error);
        $this->assertNotEqual('', $responseObject, $error);
        
        $expectedBounces = $responseObject->response->data->bounces;
        $this->assertNotNull($expectedBounces, $error);
        
        $api = new Dynect;
        $bounces = $api->getBounceList($startTime, $endTime, $type);
        
        $this->assertEqual($api->getLastStatus(), 'success');
        
        $error = 'getBounceList does not work properly.';
        $expectedBouncesCount = count($expectedBounces);
        $bouncesCount = count($bounces);
        $this->assertEqual($expectedBouncesCount, $bouncesCount, $error);
        for ($i = 0; $i < $expectedBouncesCount; $i++) {
            $thisError = "[$i] $error";
            $expected = $expectedBounces[$i];
            $actual = $bounces[$i];
            $this->assertEqual($expected->emailaddress, $actual->emailaddress, $thisError);
            $this->assertEqual($expected->bouncetype, $actual->bouncetype, $thisError);
            $this->assertEqual($expected->bouncerule, $actual->bouncerule, $thisError);
            $this->assertEqual($expected->bouncecode, $actual->bouncecode, $thisError);
            $this->assertEqual($expected->bouncetime, $actual->bouncetime, $thisError);
            $this->assertEqual($expected->notifiedtime, $actual->notifiedtime, $thisError);
            $this->assertEqual($expected->notified, $actual->notified, $thisError);
            $this->assertEqual($expected->xheaders->{'X-CONTENT-ID'}, $actual->xheaders->{'X-CONTENT-ID'}, $thisError);
        }
    }
    //--------------------------------------------------------------------------
    

    public function testDynect_getComplaintList()
    {
        $startTime = '2013-08-30';
        $endTime = '2013-09-03';
        $complaintsUrl = 'http://emailapi.dynect.net/rest/json/reports/complaints?apikey=' . Config::$espCredentials['dynect']['apikey']
            . '&noheaders=1&starttime=' . urlencode($startTime) . '&endtime=' . urlencode($endTime);
        
        $error = 'Invalid response from the complaints URL';
        $response = $this->_getUrlData($complaintsUrl);
        $responseObject = json_decode($response);
        $this->assertNotNull($responseObject, $error);
        $this->assertNotEqual('', $responseObject, $error);
        
        $expectedComplaints = $responseObject->response->data->complaints;
        $this->assertNotNull($expectedComplaints, $error);
        
        $api = new Dynect;
        $complaints = $api->getComplaintList($startTime, $endTime);
        
        $this->assertEqual($api->getLastStatus(), 'success');
        
        $error = 'getComplaintList does not work properly.';
        $expectedComplaintsCount = count($expectedComplaints);
        $complaintsCount = count($complaints);
        $this->assertEqual($expectedComplaintsCount, $complaintsCount, $error);
        for ($i = 0; $i < $expectedComplaintsCount; $i++) {
            $thisError = "[$i] $error";
            $expected = $expectedComplaints[$i];
            $actual = $complaints[$i];
            $this->assertEqual($expected->emailaddress, $actual->emailaddress, $thisError);
            $this->assertEqual($expected->date, $actual->date, $thisError);
            $this->assertEqual($expected->complainttime, $actual->complainttime, $thisError);
            $this->assertEqual($expected->notifiedtime, $actual->notifiedtime, $thisError);
            $this->assertEqual($expected->notified, $actual->notified, $thisError);
            $this->assertEqual($expected->xheaders->{'X-CONTENT-ID'}, $actual->xheaders->{'X-CONTENT-ID'}, $thisError);
        }
    }
    //--------------------------------------------------------------------------
    

    private function _getUrlData($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        
        return $response;
    }
}