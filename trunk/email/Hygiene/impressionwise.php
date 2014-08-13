<?php

require_once dirname(__FILE__) . '/../Hygiene.php';

class ImpressionWise extends Hygiene
{
    public function getRestAuthParams()
    {
        return array(
            'username'  => $this->getUsername(),
            'password'  => $this->getPassword()
        );
    }
    //--------------------------------------------------------------------------


    public function getRestUrl()
    {
        return 'http://post.impressionwise.com/fastfeed.aspx';
    }
    //--------------------------------------------------------------------------


    public function getDecision()
    {
        $parsedData = $this->getParsedResponse();
        
        if (empty($parsedData['result'])) {
            Logging::logDebugging('Empty response result - ImpressionWise Reached Default Switch-State', serialize($this->getParsedResponse()));
            Logging::logImpressionWiseResult('TIMEOUT');
            return 'retry';
        }
        
        switch (strtoupper($parsedData['result'])) {
            case "CERTDOM" :
                Logging::logImpressionWiseResult('CERTDOM');
                return 'validemail';

            case "CERTINT" :
                Logging::logImpressionWiseResult('CERTINT');
                return 'validemail';

            case "NETPROTECT" :
                Logging::logImpressionWiseResult('NETPROTECT');
                //TODO: Implement config-level variable check to control if this is considered valid or invalid
                if(1) {
                    return 'invaliddomain';
                } else {
                    return 'validemail';
                }

            case "INVALID" :
                Logging::logImpressionWiseResult('INVALID');
                return 'invalidemail';

            case "TRAP" :
                Logging::logImpressionWiseResult('TRAP');
                return 'invaliddomain';

            case "MOLE" :
                Logging::logImpressionWiseResult('MOLE');
                return 'invalidemail';

            case "QUARANTINE" :
                Logging::logImpressionWiseResult('QUARANTINE');
                return 'invalidemail';

            case "PARKED" :
                Logging::logImpressionWiseResult('PARKED');
                return 'invalidemail';

            case "SEED" :
                Logging::logImpressionWiseResult('SEED');
                return 'invalidemail';

            case "KEY" :
                Logging::logImpressionWiseResult('KEY');
                return 'invalidemail';

            case "RETRY" :
                Logging::logImpressionWiseResult('RETRY');
                return 'retry';

            case "WRONG_PSW" :
                Logging::logDebugging('ImpressionWise Password Error', NULL);
                Logging::logImpressionWiseResult('WRONG_PSW');
                return 'retry';

            default :
                Logging::logDebugging('ImpressionWise Reached Default Switch-State', serialize($this->getParsedResponse()));
                Logging::logImpressionWiseResult('TIMEOUT');
                return 'retry';
        }
    }
    //--------------------------------------------------------------------------


    public function getName()
    {
        return 'ImpressionWise';
    }
    //--------------------------------------------------------------------------


    public function processLead($email, $options = NULL)
    {
        if (!is_string($email) || empty($email)) {
            return false;
        }

        $arguments = array(
            'code' => $this->getUsername(),
            'pwd' => $this->getPassword(),
            'email' => $email
        );

        if (is_array($options)) {
            foreach ($options as $key => $value) {
                $arguments[$key] = $value;
            }
        }

        $fullRequestUrl = $this->getRestUrl() . '?' . http_build_query($arguments);
        $ch = curl_init($fullRequestUrl);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        if (!empty($response)) {
            $this->rawResponse = $response;
            $parsedResponse = array();
            $parts = explode('&', $response);
            foreach ($parts as $part) {
                $tmp = explode('=', $part);
                if (isset($tmp[0], $tmp[1])) {
                    $parsedResponse[urldecode($tmp[0])] = urldecode($tmp[1]);
                }
            }
            $this->parsedResponse = $parsedResponse;
        }

        Logging::logImpressionWiseSent();

        return true;
    }
    //--------------------------------------------------------------------------
}