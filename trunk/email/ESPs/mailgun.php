<?php

class Mailgun extends ESP
{

    public function getRestAuthParams()
    {
        return array();
    }
    //--------------------------------------------------------------------------

    public function getAuthenticationKey() 
    {
        return 'api:' . $this->getApiKey();
    }

    public function getName()
    {
        return 'Mailgun E-Mail Delivery';
    }
    //--------------------------------------------------------------------------


    public function getRestUrl()
    {
        return Config::$espCredentials[$this->getKey()]['domain'];
    }
    //--------------------------------------------------------------------------


    public function sendEmail($to, $fromPerson, $fromEmail, $subject, $bodyHtml, $bodyText, $subId, $unsubUrl, $delaySeconds, $debug = false)
    {
        $postData = array(
                    'to'            => $to,
                    'from'          => "\"{$fromPerson}\" <{$fromEmail}>",
                    'subject'       => $subject
                );
                    
        if (!empty($bodyHtml)) {
            $postData['html'] = $bodyHtml;
        } 
        if (!empty($bodyText)) {
            $postData['text'] = $bodyText;
        }
        $result = $this->restCall(
            "messages",
            array(),
            array(
                'to'            => $to,
                'from'          => "\"{$fromPerson}\" <{$fromEmail}>",
                'subject'       => $subject,
                'html'      => $bodyHtml,
                'text'      => $bodyText
            ),
            true
        );

        
        if (isset($result['httpCode']) && $result['httpCode'] == 200) {
            $this->lastStatus = 'sent';
            if ($debug === true) {
                Logging::logSendError('Mailgun', $result['httpCode'], $result['httpErrno'], $result['httpErr'], $to, $fromPerson, $fromEmail, $subject, $bodyHtml, $bodyText, $delaySeconds);
            }         
        }
        else {
            Logging::logSendError('Mailgun', $result['httpCode'], $result['httpErrno'], $result['httpErr'], $to, $fromPerson, $fromEmail, $subject, $bodyHtml, $bodyText, $delaySeconds);
            $this->lastStatus = 'error';
        }
    }
    //--------------------------------------------------------------------------

    public function report($method)
    {
        
        $result = $this->restCall(
            $method,
            array(),
            array(),
            true
        );
        
        if (isset($result['httpCode']) && $result['httpCode'] == 200) {
            $this->lastStatus = 'success';        
            $content = json_decode($result['content'], true);
            return $content['items'];
        }
        else {
            $this->lastStatus = 'error';
            return array();
        }
    }
    //--------------------------------------------------------------------------

    public function getSentMessages() 
    {
        return $this->report('log');
    }
    //--------------------------------------------------------------------------
    
    public function getUnsubscribeMessages() 
    {
        return $this->report('unsubscribes');
    }
    //--------------------------------------------------------------------------
    
    public function getSpamMessages() 
    {
        return $this->report('complaints');
    }
    //--------------------------------------------------------------------------
    
    public function getBounceMessages() 
    {
        return $this->report('bounces');
    }
    //--------------------------------------------------------------------------

    public function setStatusFromReturnCode($code)
    {
        switch ($code) {
            case '200' :
                $this->lastStatus = 'success';
                break;

            case '404' :
                $this->lastStatus = 'not_found';
                break;

            case '503' :
                $this->lastStatus = 'unavailable';
                break;

            case '451' :
                $this->lastStatus = 'invalid_api_key';
                break;

            case '452' :
                $this->lastStatus = 'invalid_fields';
                break;

            case '453' :
                $this->lastStatus = 'already_exists';
                break;
        }
    }
    //--------------------------------------------------------------------------

}