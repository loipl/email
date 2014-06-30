<?php

abstract class Hygiene
{

    protected $username     = '';
    protected $password     = '';
    protected $apiKey       = '';

    protected $rawResponse     = null;
    protected $parsedResponse  = array();

    private   $key          = false;

    public function __construct()
    {
        $this->key = strtolower(get_class($this));
        if (isset(Config::$hygieneCredentials[$this->key])) {
            $this->username = (isset(Config::$hygieneCredentials[$this->key]['username']) ? Config::$hygieneCredentials[$this->key]['username'] : false);
            $this->password = (isset(Config::$hygieneCredentials[$this->key]['password']) ? Config::$hygieneCredentials[$this->key]['password'] : false);
            $this->apiKey   = (isset(Config::$hygieneCredentials[$this->key]['apikey'])   ? Config::$hygieneCredentials[$this->key]['apikey']   : false);
        }
    }
    //--------------------------------------------------------------------------


    abstract public function getName();
    //--------------------------------------------------------------------------


    abstract public function getRestUrl();
    //--------------------------------------------------------------------------


    abstract public function getRestAuthParams();
    //--------------------------------------------------------------------------


    abstract public function processLead($email, $options);
    //-------------------------------------------------------------------------


    abstract public function getDecision();
    //-------------------------------------------------------------------------


    public function getUsername()
    {
        return $this->username;
    }
    //--------------------------------------------------------------------------


    public function getPassword()
    {
        return $this->password;
    }
    //--------------------------------------------------------------------------


    public function getApiKey()
    {
        return $this->apiKey;
    }
    //--------------------------------------------------------------------------


    public function getRawResponse()
    {
        return $this->rawResponse;
    }
    //--------------------------------------------------------------------------


    public function getParsedResponse()
    {
        return $this->parsedResponse;
    }
    //--------------------------------------------------------------------------


    public function isParsed()
    {
        return !empty($this->parsedResponse);
    }
    //--------------------------------------------------------------------------


    public function getNPD()
    {
        if (isset($this->parsedResponse['NPD'])) {
            return $this->parsedResponse['NPD'];
        }
        return null;
    }
    //--------------------------------------------------------------------------


    public function getResult()
    {
        if (isset($this->parsedResponse['result'])) {
            return $this->parsedResponse['result'];
        }
        return null;
    }
    //--------------------------------------------------------------------------


    public function getTTP() {
        if (isset($this->parsedResponse['TTP'])) {
            return $this->parsedResponse['TTP'];
        }
        return null;
    }
    //--------------------------------------------------------------------------
}