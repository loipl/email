<?php

require_once dirname(__FILE__) . '/../Verification.php';

class LeadSpend extends Verification
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
        return 'https://primary.api.leadspend.com/v2/validity';
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
            return;
        }

        $arguments = array(
            'key' => $this->getApiKey(),
            'timeout' => $this->timeout
        );

        if (is_array($options)) {
            foreach ($options as $key => $value) {
                $arguments[$key] = $value;
            }
        }

        $fullRequestUrl = $this->getRestUrl() . '/' . $email . '?' . http_build_query($arguments);

        $ch = curl_init($fullRequestUrl);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        if (!empty($response)) {
            $this->rawResponse = $response;
            $parsedResponse = json_decode($response, true);
            $this->parsedResponse = $parsedResponse;
        }
    }
    //--------------------------------------------------------------------------
}