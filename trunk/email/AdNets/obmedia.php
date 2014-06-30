<?php

class OBMedia extends AdNet
{
    protected $sendDomain   = '';
    protected $token        = '';
    
    protected $xmlResult    = '';
    protected $resultEmails = array();
    protected $resultErrors = array();

    public function __construct()
    {
        parent::__construct();
        $key = strtolower(get_class($this));
        
        if (isset(Config::$adNetCredentials[$key])) {
            $this->sendDomain = (isset(Config::$adNetCredentials[$key]['sendDomain']) ? Config::$adNetCredentials[$key]['sendDomain'] : false);
            $this->token      = (isset(Config::$adNetCredentials[$key]['token'])      ? Config::$adNetCredentials[$key]['token']      : false);
        }
    }
    //--------------------------------------------------------------------------


    public function getRestUrl()
    {
        return 'http://api-2.obmedia.com';
    }
    //--------------------------------------------------------------------------


    public function getToken()
    {
        return $this->token;
    }
    //--------------------------------------------------------------------------


    public function getSendDomain()
    {
        return $this->sendDomain;
    }
    //--------------------------------------------------------------------------


    public function getName()
    {
        return 'ObMedia';
    }
    //--------------------------------------------------------------------------

    
    protected function buildXmlRequest($emailBatch)
    {
        foreach ($emailBatch as $value) {
            $email       = strtolower(trim($value['email']));
            $md5Email    = md5($email);
            $emailArray  = explode("@", $email);
            $emailDomain = $emailArray[1];

            $temp = array(
                'email'=>$email,
                'domain'=>$emailDomain,
                'subid'=>$value['campaign'],
                'countrycode'=>$value['country'],
                'state'=>$value['state'],
                'postalcode'=>$value['postal_code'],
                'gender'=>$value['gender'],
                'dayofbirth'=>$value['birth_day'],
                'monthofbirth'=>$value['birth_month'],
                'yearofbirth'=>$value['birth_year'],
                         );

            $obRequests[$md5Email] = $temp;
        }
        
        $xmlRequest =  '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xmlRequest .= '<request>' . "\n";

        if (empty($obRequests)) {
            Logging::logDebugging('obRequests empty in OBMedia::buildXmlRequest', serialize($emailBatch));
        }

        foreach ($obRequests as $key => $value) {
            $xmlRequest .= '<email>' . "\n";
            $xmlRequest .= '<recipient>' . $key . '</recipient>' . "\n";
            $xmlRequest .= '<domain>' . $value['domain'] . '</domain>' . "\n";

            $xmlRequest .= ($value['subid'] != '')       ? '<subid>' . $value['subid'] . '</subid>' . "\n"                   : null;
            $xmlRequest .= ($value['countrycode'] != '') ? '<countrycode>' . $value['countrycode'] . '</countrycode>' . "\n" : null;
            $xmlRequest .= ($value['state'] != '')       ? '<state>' . $value['state'] . '</state>' . "\n"                   : null;
            $xmlRequest .= ($value['postalcode'] != '')  ? '<postalcode>' . $value['postalcode'] . '</postalcode>' . "\n"    : null;
            $xmlRequest .= ($value['gender'] != '')      ? '<gender>' . $value['gender'] . '</gender>' . "\n"                : null;

            if ($value['dayofbirth'] != '' && $value['dayofbirth'] != 1 && $value['monthofbirth'] != 1) {
                $xmlRequest .= '<dayofbirth>' . $value['dayofbirth'] . '</dayofbirth>' . "\n";
                $xmlRequest .= ($value['monthofbirth'] != '') ? '<monthofbirth>' . $value['monthofbirth'] . '</monthofbirth>' . "\n" : null;
            }

            $xmlRequest .= ($value['yearofbirth'] != '') ?  '<yearofbirth>' . $value['yearofbirth'] . '</yearofbirth>' . "\n" : null;
            $xmlRequest .= '</email>' . "\n";
        }

        $xmlRequest .= '</request>' . "\n";
        
        return $xmlRequest;
    }
    //--------------------------------------------------------------------------


    public function send($emailBatch = array(), $test = '0')
    {
        $sendDomain = $this->getSendDomain();
        $idomain    = 'obm.' . $sendDomain;
        $token      = $this->getToken();
        $cdomain    = $idomain;
        
        $xmlRequest = $this->buildXmlRequest($emailBatch);

        if (empty($xmlRequest)) {
            Logging::logDebugging('xmlRequest empty in OBMedia::send', serialize($emailBatch));
        }

        $query  = 'token='   . $token     . '&';
        $query .= 'test='    . $test      . '&';
        $query .= 'idomain=' . $idomain   . '&';
        $query .= 'cdomain=' . $cdomain   . '&';
        $query .= 'request=' . $xmlRequest;

        $ch = curl_init($this->getRestUrl());
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        curl_setopt($ch, CURLOPT_SSLVERSION, 3);

        if (curl_errno($ch)) {
            $this->lastStatus = 'fail';
            $this->lastError = 'Curl error communicating with OBMedia: ' . curl_error($ch);
            Logging::logDebugging('Curl error communicating with OBMedia', curl_error($ch));
            return false;
        }

        $ch_result = curl_exec($ch);
        curl_close($ch);

        if ($this->parseResult($ch_result)) {
            $this->lastStatus = 'success';
            return true;
        } else {
            $this->lastStatus = 'fail';
            $this->lastError = 'SimpleXML error parsing XML from OBMedia response';
            Logging::logDebugging('SimpleXML error parsing XML from OBMedia response', null);
            return false;
        }
    }
    //--------------------------------------------------------------------------


    protected function parseResult($xml)
    {
        $xmlResult = simplexml_load_string($xml, null, LIBXML_NOCDATA);

        if (empty($xmlResult)) {
            Logging::logDebugging('xmlResult empty in OBMedia::parseResult', serialize($xml));
            return false;
        }

        $this->xmlResult = $xmlResult;

        foreach ($xmlResult->error as $error) {
            $this->resultErrors[] = $error;
        }
        foreach ($xmlResult->email as $email) {
            $this->resultEmails[] = $email;
        }

        return true;
    }
    //--------------------------------------------------------------------------


    public function getResultEmails()
    {
        return $this->resultEmails;
    }
    //--------------------------------------------------------------------------


    public function getResultErrors()
    {
        return $this->resultErrors;
    }
    //--------------------------------------------------------------------------


    public function getResult()
    {
        return $this->xmlResult;
    }
    //--------------------------------------------------------------------------


    public function getResultCount()
    {
        if (isset($this->xmlResult->email)) {
            return count($this->xmlResult->email);
        } else {
            return '0';
        }
    }
    //--------------------------------------------------------------------------


    public function getFriendlyFrom($interval)
    {
        return $this->xmlResult->email[$interval]->creative->friendlyfrom;
    }
    //--------------------------------------------------------------------------


    public function getSubject($interval)
    {
        return $this->xmlResult->email[$interval]->creative->subject;
    }
    //--------------------------------------------------------------------------


    public function getBody($interval)
    {
        return $this->xmlResult->email[$interval]->creative->body;
    }
    //--------------------------------------------------------------------------


    public function getTextBody($interval)
    {
        return $this->xmlResult->email[$interval]->creative->textbody;
    }
    //--------------------------------------------------------------------------


    public function getCategoryId($interval)
    {
        return $this->xmlResult->email[$interval]->categories->categoryid;
    }
    //--------------------------------------------------------------------------


    public function getFullObject($interval)
    {
        return $this->xmlResult->email[$interval];
    }
    //--------------------------------------------------------------------------


    public function buildPayload($leads)
    {
        foreach ($leads as $lead) {
            $emails[] = array(
                'email'       => $lead['email'],
                'campaign'    => $lead['source_campaign'],
                'country'     => $lead['country'],
                'state'       => $lead['state'],
                'postal_code' => $lead['postal_code'],
                'gender'      => $lead['gender'],
                'birth_day'   => $lead['birth_year'],
                'birth_month' => $lead['birth_month'],
                'birth_year'  => $lead['birth_year']
            );
        }

        if (!empty($emails)) {
            return $emails;
        } else {
            Logging::logDebugging('Payload (emails) empty in OBMedia::buildPayload', serialize($leads));
            return false;
        }
    }
    //--------------------------------------------------------------------------


    public function buildArrayByRecipientKey($leadData)
    {
        for ($x = 0, $count = $this->getResultCount(); $x < $count; $x++) {
            if (isset($leadData[(string)$this->xmlResult->email[$x]->recipient])) {
                $keyedArray[] = array(
                    'email'          => $leadData[(string)$this->xmlResult->email[$x]->recipient]['email'],
                    'subject'        => (string)$this->xmlResult->email[$x]->creative->subject,
                    'friendlyFrom'   => (string)$this->xmlResult->email[$x]->creative->friendlyfrom,
                    'htmlBody'       => (string)$this->xmlResult->email[$x]->creative->body,
                    'textBody'       => (string)$this->xmlResult->email[$x]->creative->textbody,
                    'categoryId'     => (string)$this->xmlResult->email[$x]->categories->categoryid,
                    'build_queue_id' => $leadData[(string)$this->xmlResult->email[$x]->recipient]['build_queue_id']
                );
            }
        }

        if (!empty($keyedArray)) {
            return $keyedArray;
        } else {
            Logging::logDebugging('keyedArray empty in OBMedia::buildArrayByRecipientKey (' . $x . ' of ' . $count . ')', 'lastResult: ' . $this->lastStatus . ' resultErrors: ' . serialize($this->resultErrors) . ' leadData: ' . serialize($leadData));
            return false;
        }
    }
    //--------------------------------------------------------------------------
}
