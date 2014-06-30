<?php

class SmtpCom extends ESP
{
    const SMTP_API_URL = "http://api.smtp.com/v1/";
    
    private $_smtpObject;

    public function __construct() {
        parent::__construct();  
        $this->_smtpObject = new SMTP($this->getSMTPConfig());
    }

    public function getRestAuthParams()
    {
        return array(
            'apikey'  => $this->getApiKey()
        );
    }
    //--------------------------------------------------------------------------
    
    
    public function getSMTPConfig()
    {
        return array(
            'host'      => $this->getHost(),
            'user'      => $this->getUsername(),
            'password'  => $this->getPassword(),
            'port'      => $this->getPort(),
            'sshTunnel' => false,
        );
        
    }
    //--------------------------------------------------------------------------


    public function getName()
    {
        return 'Smtp.com E-Mail Delivery';
    }
    //--------------------------------------------------------------------------


    public function getRestUrl()
    {
        return self::SMTP_API_URL;
    }

    public function sendEmail($to, $fromPerson, $fromEmail, $subject, $bodyHtml, $bodyText, $subId, $unsubUrl, $debug = false)
    {
        $seperator = '';
        $isHtml = FALSE;
        if(!empty($bodyHtml)) 
        {
            $isHtml = TRUE;
            if (!empty($bodyText)) {
                $seperator = "\n\r";
            }
        }
        $body = $bodyHtml . $seperator . $bodyText;
        $this->_smtpObject->sendEmail($to, $fromPerson, $fromEmail, $subject, $body, $isHtml);
        $this->lastStatus   = $this->_smtpObject->getLastStatus();
        $this->lastError    = $this->_smtpObject->getLastError();
    }
    //--------------------------------------------------------------------------


    public function setStatusFromReturnCode($code)
    {
        
    }

}