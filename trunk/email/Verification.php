<?php

abstract class Verification
{
    protected $username;
    protected $password;
    protected $apiKey;

    protected $timeout  = 5;

    protected $rawResponse     = null;
    protected $parsedResponse  = array();

    public function __construct()
    {
        $this->key = strtolower(get_class($this));
        if (isset(Config::$verificationCredentials[$this->key])) {
            $this->username = (isset(Config::$verificationCredentials[$this->key]['username']) ? Config::$verificationCredentials[$this->key]['username'] : false);
            $this->password = (isset(Config::$verificationCredentials[$this->key]['password']) ? Config::$verificationCredentials[$this->key]['password'] : false);
            $this->apiKey   = (isset(Config::$verificationCredentials[$this->key]['apikey'])   ? Config::$verificationCredentials[$this->key]['apikey']   : false);
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


    public function getResult()
    {
        if (isset($this->parsedResponse['result'])) {
            return $this->parsedResponse['result'];
        }
        return null;
    }
    //--------------------------------------------------------------------------
}