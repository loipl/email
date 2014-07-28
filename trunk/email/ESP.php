<?php

abstract class ESP
{
    protected $username     = '';
    protected $password     = '';
    protected $apiKey       = '';
    protected $host         = '';
    protected $port         = '';

    protected $lastStatus   = '';
    protected $lastResponse = '';
    protected $lastError    = '';

    private   $key          = false;

    public function __construct()
    {
        $this->key = strtolower(get_class($this));
        if (isset(Config::$espCredentials[$this->key])) {
            $this->username = (isset(Config::$espCredentials[$this->key]['username']) ? Config::$espCredentials[$this->key]['username'] : false);
            $this->password = (isset(Config::$espCredentials[$this->key]['password']) ? Config::$espCredentials[$this->key]['password'] : false);
            $this->host     = (isset(Config::$espCredentials[$this->key]['host'])     ? Config::$espCredentials[$this->key]['host']     : false);
            $this->port     = (isset(Config::$espCredentials[$this->key]['port'])     ? Config::$espCredentials[$this->key]['port']     : false);
            $this->apiKey   = (isset(Config::$espCredentials[$this->key]['apikey'])   ? Config::$espCredentials[$this->key]['apikey']   : false);
        }
    }
    //--------------------------------------------------------------------------


    abstract public function getName();
    //--------------------------------------------------------------------------


    abstract public function getRestUrl();
    //--------------------------------------------------------------------------


    abstract public function getRestAuthParams();
    //--------------------------------------------------------------------------


    abstract public function sendEmail($to, $fromPerson, $fromEmail, $subject, $bodyHtml, $bodyText, $subId, $unsubUrl,$delaySeconds, $debug = false);
    //--------------------------------------------------------------------------


    abstract public function setStatusFromReturnCode($code);
    //--------------------------------------------------------------------------

    
    public function getLastError()
    {
        return $this->lastError;
    }
    //--------------------------------------------------------------------------

    public function getLastStatus()
    {
        return $this->lastStatus;
    }
    //--------------------------------------------------------------------------


    public function getLastResponse()
    {
        return $this->lastResponse;
    }
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
    
    public function getHost()
    {
        return $this->host;
    }
    //--------------------------------------------------------------------------


    public function getPort()
    {
        return $this->port;
    }
    //--------------------------------------------------------------------------


    public function getApiKey()
    {
        return $this->apiKey;
    }
    //--------------------------------------------------------------------------
    
    
    
    protected function restCall($method, $paramsGet=array(), $paramsPost=array(), $curl_USERPWD_verify = false)
    {
        $url = $this->getRestUrl() . $method;
        if ($paramsGet) {
            $url .= "?" . http_build_query(array_merge($this->getRestAuthParams(), $paramsGet));
        }
        else if ($paramsPost) {
            $paramsPost = array_merge($this->getRestAuthParams(), $paramsPost);
        }
        else {
            $url .= "?" . http_build_query($this->getRestAuthParams());
        }
        
        $options = array(
            CURLOPT_URL				=> $url,
            CURLOPT_FOLLOWLOCATION	=> true,
            CURLOPT_AUTOREFERER		=> true,
            CURLOPT_RETURNTRANSFER	=> true,
            CURLOPT_TIMEOUT			=> 60
        );
        
        if ($paramsPost) {
            $options[CURLOPT_POST]          = true;
            $options[CURLOPT_POSTFIELDS]    = $paramsPost;
        }

        $ch = curl_init();
        curl_setopt_array($ch, $options);

        if ($curl_USERPWD_verify) {
            curl_setopt($ch, CURLOPT_USERPWD, $this->getAuthenticationKey());
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        }
        
        $content    = curl_exec($ch);
        $httpCode   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err        = curl_error($ch);
        $errno      = curl_errno($ch);
        curl_close($ch);

        return array(
            'httpCode'  => $httpCode,
            'httpErr'   => $err,
            'httpErrno' => $errno,
            'content'   => $content
        );
    }
    //--------------------------------------------------------------------------
}