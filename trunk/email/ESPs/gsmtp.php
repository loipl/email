<?php

class GSMTP extends SMTP
{
    
    public function sendEmail($to, $fromPerson, $fromEmail, $subject, $htmlBody, $textBody)
    {
        return parent::sendEmail($to, $fromPerson, $fromEmail, $subject, $htmlBody, $textBody);
    }
    
    
    function tests()
    {
        $res = $this->sendEmail(Config::$emailTests, "System Test", Config::$smtp['user'], "Testing Subject", "Testing Message", "Text Testing Message", '5');

        if (!$res) {
            echo "error send email: " . $this->getLastError();
        }
        /*
        $res = $this->connect();
        if ( !$res ) {
            echo "connect error: " . $this->getLastError(); return;
        }
        
        $res = $this->authenticate();
        if ( !$res ) {
            echo "authenticate error: " . $this->getLastError(); return;
        }
        
        
        print_r(stream_get_meta_data($this->socket));
         */
        
        //echo $this->readFromSmtp();
        
        //print_r(stream_get_meta_data($this->socket));
    }
}