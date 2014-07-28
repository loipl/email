<?php

class Dynect extends ESP
{

    public function getRestAuthParams()
    {
        return array(
            'apikey'  => $this->getApiKey()
        );
    }
    //--------------------------------------------------------------------------


    public function getName()
    {
        return 'Dynect E-Mail Delivery';
    }
    //--------------------------------------------------------------------------


    public function getRestUrl()
    {
        return 'http://emailapi.dynect.net/rest/';
    }
    //--------------------------------------------------------------------------


    public function sendEmail($to, $fromPerson, $fromEmail, $subject, $bodyHtml, $bodyText, $subId, $unsubUrl, $delaySeconds, $debug = false)
    {
        if (!empty($bodyHtml) && !empty($bodyText)) {
            $result = $this->restCall(
                "json/send",
                array(),
                array(
                    'to'            => $to,
                    'from'          => "\"{$fromPerson}\" <{$fromEmail}>",
                    'subject'       => $subject,
                    'bodyhtml'      => $bodyHtml,
                    'bodytext'      => $bodyText,
                    'X-Activity-ID' => $subId,
                    'List-Unsubscribe' => '<' . $unsubUrl . '>'
                )
            );
        } else if (!empty($bodyHtml) && empty($bodyText)) {
            $result = $this->restCall(
                "json/send",
                array(),
                array(
                    'to'            => $to,
                    'from'          => "\"{$fromPerson}\" <{$fromEmail}>",
                    'subject'       => $subject,
                    'bodyhtml'      => $bodyHtml,
                    'X-Activity-ID' => $subId,
                    'List-Unsubscribe' => '<' . $unsubUrl . '>'
                )
            );
        } else if (empty($bodyHtml) && !empty($bodyText)) {
            $result = $this->restCall(
                "json/send",
                array(),
                array(
                    'to'            => $to,
                    'from'          => "\"{$fromPerson}\" <{$fromEmail}>",
                    'subject'       => $subject,
                    'bodytext'      => $bodyText,
                    'X-Activity-ID' => $subId,
                    'List-Unsubscribe' => '<' . $unsubUrl . '>'
                )
            );
        }

        $response = json_decode($result['content']);
        
        if (isset($response->response->status) && $response->response->status == 200) {
            $this->lastStatus = 'sent';
            if ($debug === true) {
                Logging::logSendError('DynECT', $result['httpCode'], $result['httpErrno'], $result['httpErr'], $to, $fromPerson, $fromEmail, $subject, $bodyHtml, $bodyText, $delaySeconds);
            }
            
            $data = $response->response->data;
            if ( preg_match('/.*Error:.*/', $data) ) {
                Logging::logSendError('DynECT', $result['httpCode'], $result['httpErrno'], $result['httpErr'], $to, $fromPerson, $fromEmail, $subject, $bodyHtml, $bodyText, $delaySeconds);
                $this->lastStatus = 'error';
            }
        }
        else {
            Logging::logSendError('DynECT', $result['httpCode'], $result['httpErrno'], $result['httpErr'], $to, $fromPerson, $fromEmail, $subject, $bodyHtml, $bodyText, $delaySeconds);
            $this->lastStatus = 'error';
        }
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

    
    private function callListMethod($method, $params, $listKey)
    {
        
        $result = $this->restCall("json/{$method}/count", $params);

        $response   = json_decode($result['content']);
        $status     = $response->response->status;
        if ($status != 200) {
            $this->setStatusFromReturnCode($status);
            return false;
        }
        
        $count = $response->response->data->count;

        $list = array();
        for ($start=0; $start < $count; $start = $start + 500) {
            $result = $this->restCall(
                "json/{$method}", 
                array_merge($params, array('startindex' => $start))
            );
            $response   = json_decode($result['content']);
            $status     = $response->response->status;
            if ($status != 200) {
                $this->setStatusFromReturnCode($status);
                return false;
            }
            $list = array_merge($list, $response->response->data->{$listKey});
        }

        $this->lastStatus = 'success';
        return $list;
    }
    
    
    public function getSuppressionList($start, $end)
    {
        return $this->callListMethod("suppressions", array('startdate' => $start, 'enddate' => $end), "suppressions");
    }
    //--------------------------------------------------------------------------


    public function getComplaintList($start, $end)
    {
        return $this->callListMethod("reports/complaints", array('startdate' => $start, 'enddate' => $end), "complaints");
    }
    //--------------------------------------------------------------------------


    public function getBounceList($start, $end, $type)
    {
        return $this->callListMethod("reports/bounces", array('startdate' => $start, 'enddate' => $end, 'bouncetype' => $type, 'noheaders' => '1'), "bounces");
    }
    //--------------------------------------------------------------------------


    public function getSuppressionStatus($email)
    {
        $result = $this->restCall("json/recipients/status", array('emailaddress' => $email));
        
        $response   = json_decode($result['content']);
        $status     = $response->response->status;
        if ($status != 200) {
            $this->setStatusFromReturnCode($status);
            return false;
        }
        $this->setStatusFromReturnCode($status);        
        
        foreach ($response->response->data->recipients AS $recipient) {
            if ($recipient->emailaddress == $email && $recipient->suppressionstatus) {
                return $recipient->suppressionstatus;
            }
        }
        
        return '';
    }
    //--------------------------------------------------------------------------


    public function getReportSuccesses($start, $end)
    {
        return $this->callListMethod("reports/delivered", array('startdate' => $start, 'enddate' => $end), "delivered");
    }
    //--------------------------------------------------------------------------


    public function getReportProblems($start, $end)
    {
        return $this->callListMethod("reports/issues", array('startdate' => $start, 'enddate' => $end), "issue");
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