<?php

class ADKI extends AdNet
{
    protected $sendDomain   = '';
    protected $token        = '';
    
    protected $xmlResult    = '';
    protected $resultEmails = array();
    protected $resultErrors = array();

    protected $domainMap = array(
        'default' => array(
            'default'        => '13873',
            'aol.com'        => '13874',
            'aol.co.uk'      => '13874',
            'aim.com'        => '13874',
            'cs.com'         => '13874',
            'compuserve.com' => '13874',
            'netscape.net'   => '13874',
            'netscape.com'   => '13874',
            'netscape.co.uk' => '13874',
            'wmconnect.com'  => '13874',
            'yahoo.com'      => '13875',
            'yahoo.ca'       => '13875',
            'yahoo.es'       => '13875',
            'yahoo.fr'       => '13875',
            'yahoo.co.uk'    => '13875',
            'yahoomail.com'  => '13875',
            'ymail.com'      => '13875',
            'geocities.com'  => '13875',
            'rocketmail.com' => '13875',
            'hotmail.ca'     => '13876',
            'hotmail.fr'     => '13876',
            'hotmail.es'     => '13876',
            'msn.co.uk'      => '13876',
            'outlook.co.uk'  => '13876',
            'hotmail.com'    => '13876',
            'hotmail.co.uk'  => '13876',
            'msn.com'        => '13876',
            'email.msn.com'  => '13876',
            'live.com'       => '13876',
            'live.ca'        => '13876',
            'live.co.uk'     => '13876',
            'outlook.com'    => '13876',
            'q.com'          => '13876',
            'gmail.com'      => '13877',
            'googlemail.com' => '13877',
            'juno.com'       => '13998',
            'juno.co.uk'     => '13998',
            'netzero.com'    => '13998',
            'netzero.net'    => '13998',
        ),
        'clickers' => array(
            'clickers'       => '15195',
            'aol.com'        => '15196',
            'aol.co.uk'      => '15196',
            'aim.com'        => '15196',
            'cs.com'         => '15196',
            'compuserve.com' => '15196',
            'netscape.net'   => '15196',
            'netscape.com'   => '15196',
            'netscape.co.uk' => '15196',
            'wmconnect.com'  => '15196',
            'yahoo.com'      => '15373',
            'yahoo.ca'       => '15373',
            'yahoo.es'       => '15373',
            'yahoo.fr'       => '15373',
            'yahoo.co.uk'    => '15373',
            'yahoomail.com'  => '15373',
            'ymail.com'      => '15373',
            'geocities.com'  => '15373',
            'rocketmail.com' => '15373',
            'hotmail.com'    => '15374',
            'hotmail.co.uk'  => '15374',
            'hotmail.ca'     => '15374',
            'hotmail.fr'     => '15374',
            'hotmail.es'     => '15374',
            'msn.co.uk'      => '15374',
            'outlook.co.uk'  => '15374',
            'msn.com'        => '15374',
            'email.msn.com'  => '15374',
            'live.com'       => '15374',
            'live.ca'        => '15374',
            'live.co.uk'     => '15374',
            'outlook.com'    => '15374',
            'q.com'          => '15374',
            'gmail.com'      => '15513',
            'googlemail.com' => '15513',
            'juno.com'       => '15514',
            'juno.co.uk'     => '15514',
            'netzero.com'    => '15514',
            'netzero.net'    => '15514',
        ),
        'openers' => array(
            'openers'        => '13996',
        )
    );
//Missing from list: 13997 & 15515-15519
    protected $categoryMap = array(
        1                => 1,
        2                => 2,
        3                => 3,
        4                => 4,
    );


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
        return 'http://integrated.adstation.com/1.3';
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
        return 'AdKnowledge';
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
                'list'         => $value['list_id'],
                'email'        => $email,
                'domain'       => $emailDomain,
                'subid'        => $value['campaign'],
                'countrycode'  => $value['country'],
                'state'        => $value['state'],
                'postalcode'   => $value['postal_code'],
                'gender'       => $value['gender'],
                'dayofbirth'   => $value['birth_day'],
                'monthofbirth' => $value['birth_month'],
                'yearofbirth'  => $value['birth_year'],
                         );

            $adkiRequests[$md5Email] = $temp;
        }

        $xmlRequest =  '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xmlRequest .= '<request>' . "\n";

        if (empty($adkiRequests)) {
            Logging::logDebugging('adkiRequests empty in ADKI::buildXmlRequest', serialize($emailBatch));
        }

        foreach ($adkiRequests as $key => $value) {
            $xmlRequest .= '<email>' . "\n";
            $xmlRequest .= '<recipient>' . $key . '</recipient>' . "\n";
            $xmlRequest .= '<list>' . $value['list'] . '</list>' . "\n";
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
        $idomain    = 'adki.' . $sendDomain;
        $token      = $this->getToken();
        $cdomain    = $idomain;

        $xmlRequest = $this->buildXmlRequest($emailBatch);

        if (empty($xmlRequest)) {
            Logging::logDebugging('xmlRequest empty in ADKI::send', serialize($emailBatch));
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
            $this->lastError = 'Curl error communicating with ADKi: ' . curl_error($ch);
            Logging::logDebugging('Curl error communicating with ADKI', curl_error($ch));
            return false;
        }

        $ch_result = curl_exec($ch);
        curl_close($ch);

        if ($this->parseResult($ch_result)) {
            $this->lastStatus = 'success';
            return true;
        } else {
            $this->lastStatus = 'fail';
            $this->lastError = 'SimpleXML error parsing XML from Adknowledge response';
            Logging::logDebugging('SimpleXML error parsing XML from Adknowledge response', 'NULL Response from ADKI; ' . serialize($emailBatch));
            return false;
        }
    }
    //--------------------------------------------------------------------------


    protected function parseResult($xml)
    {
        $xmlResult = simplexml_load_string($xml, null, LIBXML_NOCDATA);

        if (empty($xmlResult)) {
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
        if (isset($this->xmlResult->email[$interval]->creative->subject)) {
            return $this->xmlResult->email[$interval]->creative->subject;
        } else {
            return false;
        }
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
        return $this->xmlResult->email[$interval]->categoryid;
    }
    //--------------------------------------------------------------------------


    private function getListId($email)
    {
        $email       = strtolower(trim($email));
        $md5Email    = md5($email);
        $emailArray  = explode("@", $email);
        $emailDomain = $emailArray[1];

        $type = 'default';

        if (Lead::isClicker($email)) {
            $type = 'clickers';
        }

        if ($type == 'default') {
            if (Lead::isOpener($email)) {
                $type = 'openers';
            }
        }

        if (array_key_exists($emailDomain, $this->domainMap[$type])) {
            return $this->domainMap[$type][$emailDomain];
        } else {
            return $this->domainMap[$type][$type];
        }
    }
    //--------------------------------------------------------------------------


    public function buildPayload($leads, $forceListId = null)
    {
        foreach ($leads as $lead) {
            if ($forceListId > 0) {
                $listId = $forceListId;
            } else {
                $listId = self::getListId($lead['email']);
            }

            $emails[] = array(
                'email'       => $lead['email'],
                'campaign'    => $lead['source_campaign'],
                'list_id'     => $listId,
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
            Logging::logDebugging('Payload (emails) empty in ADKI::buildPayload', serialize($leads));
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
                    'categoryId'     => (string)$this->xmlResult->email[$x]->categoryid,
                    'build_queue_id' => $leadData[(string)$this->xmlResult->email[$x]->recipient]['build_queue_id']
                );
            }
        }

        if (!empty($keyedArray)) {
            return $keyedArray;
        } else {
            return false;
        }
    }
    //--------------------------------------------------------------------------
}
