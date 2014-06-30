<?php

class Sendgrid extends ESP
{

    public function getRestAuthParams()
    {
        return array(
            'api_user'  => $this->getUsername(),
            'api_key'   => $this->getApiKey() 
        );
    }
    
    public function getName()
    {
        return 'SendGrid';
    }
    //--------------------------------------------------------------------------


    public function getRestUrl()
    {
        return 'http://sendgrid.com/api/';
    }
    //--------------------------------------------------------------------------


    public function sendEmail($to, $fromPerson, $fromEmail, $subject, $htmlBody, $textBody, $subId, $unsubUrl, $debug = false)
    {
        $result = $this->restCall(
            "mail.send.json",
            array(
                'to'        => $to,
                'from'      => $fromEmail,
                'fromname'  => $fromPerson,
                'subject'   => $subject,
                'html'      => $htmlBody
            )
        );

        $response = json_decode($result['content']);
        
        if ($response->message == 'success') {
            $this->lastStatus = 'sent';
        } else {
            $this->lastStatus = 'error';
        }
    }
    //--------------------------------------------------------------------------


    public function setStatusFromReturnCode($code)
    {
        switch (true) {
            case ($code >= '200' && $code <= '299') :
                $this->lastStatus = 'success';
                break;

            case ($code >= '400' && $code <= '499') :
                $this->lastStatus = 'error';
                break;

            case ($code >= '500' && $code <= '599') :
                $this->lastStatus = 'failure';
                break;
        }
    }
    //--------------------------------------------------------------------------

    
    private function callListMethod($method, $params)
    {
        $result = $this->restCall($method, $params);
        $this->setStatusFromReturnCode($result['httpCode']);
        
        return json_decode($result['content']);
    }
    //--------------------------------------------------------------------------


    public function getBlocks($startDate, $endDate)
    {
        return $this->callListMethod("blocks.get.json", array('start_date' => $startDate, 'end_date' => $endDate, 'date' => 1));
    }
    //--------------------------------------------------------------------------


    public function getBounces($startDate, $endDate)
    {
        return $this->callListMethod("bounces.get.json", array('start_date' => $startDate, 'end_date' => $endDate, 'date' => 1));
    }
    //--------------------------------------------------------------------------


    public function getInvalidList($startDate, $endDate)
    {
        return $this->callListMethod("invalidemails.get.json", array('start_date' => $startDate, 'end_date' => $endDate, 'date' => 1));
    }
    //--------------------------------------------------------------------------


    public function getSpamList($startDate, $endDate)
    {
        return $this->callListMethod("spamreports.get.json", array('start_date' => $startDate, 'end_date' => $endDate, 'date' => 1));
    }
    //--------------------------------------------------------------------------


    public function getUnsubscribes($startDate, $endDate)
    {
        return $this->callListMethod("unsubscribes.get.json", array('start_date' => $startDate, 'end_date' => $endDate, 'date' => 1));
    }
    //--------------------------------------------------------------------------


    public function getStatistics($startDate, $endDate)
    {
        return $this->callListMethod("stats.get.json", array('start_date' => $startDate, 'end_date' => $endDate));
    }
    //--------------------------------------------------------------------------


    private function initCurl()
    {
    }
    //--------------------------------------------------------------------------


    private function processRequest()
    {
    }
    //--------------------------------------------------------------------------
}