<?php

abstract class AdNet
{
    protected $username     = '';
    protected $password     = '';
    protected $apiKey       = '';

    protected $lastStatus   = '';
    protected $lastResponse = '';
    protected $lastError    = '';

    private $key            = false;

    public function __construct()
    {
        $this->key = strtolower(get_class($this));
        if (isset(Config::$adNetCredentials[$this->key])) {
            $this->username = (isset(Config::$adNetCredentials[$this->key]['username']) ? Config::$adNetCredentials[$this->key]['username'] : false);
            $this->password = (isset(Config::$adNetCredentials[$this->key]['password']) ? Config::$adNetCredentials[$this->key]['password'] : false);
            $this->apiKey   = (isset(Config::$adNetCredentials[$this->key]['apikey'])   ? Config::$adNetCredentials[$this->key]['apikey']   : false);
        }
    }
    //--------------------------------------------------------------------------


    abstract public function getName();
    //--------------------------------------------------------------------------


    public function getKey()
    {
        return $this->key;
    }
    //--------------------------------------------------------------------------


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


    public function getLastStatus()
    {
        return $this->lastStatus;
    }
    //--------------------------------------------------------------------------


    public function getLastError()
    {
        return $this->lastError;
    }
    //--------------------------------------------------------------------------
}
