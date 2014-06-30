<?php

abstract class DataAppend
{

    protected $username     = '';
    protected $password     = '';
    protected $apiKey       = '';

    protected $lastStatus   = '';
    protected $lastResponse = '';
    protected $lastError    = '';

    protected $cache        = true;
    protected $cacheTimeout = 86400;

    protected $tokens       = array();

    private   $key          = false;

    public function __construct()
    {
        $this->key = strtolower(get_class($this));

        if (isset(Config::$dataAppendCredentials[$this->key])) {
            $this->username = (isset(Config::$dataAppendCredentials[$this->key]['username']) ? Config::$dataAppendCredentials[$this->key]['username'] : false);
            $this->password = (isset(Config::$dataAppendCredentials[$this->key]['password']) ? Config::$dataAppendCredentials[$this->key]['password'] : false);
            $this->apiKey   = (isset(Config::$dataAppendCredentials[$this->key]['apikey'])   ? Config::$dataAppendCredentials[$this->key]['apikey']   : false);
        }
    }
    //--------------------------------------------------------------------------


    public final function lookup($email)
    {
        $cachedData = new CacheDataAppend($email);

        if ($cachedData->getDatetime()) {
            return $cachedData->getResult();
        }

        return $this->executeLookup($email);
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