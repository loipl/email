<?php

class Channel extends Database
{

    protected $id;
    protected $name;
    protected $type;
    protected $class;
    protected $smtp_host;
    protected $smtp_port;
    protected $smtp_user;
    protected $smtp_pass;

    protected $tableName = 'channels';
    const      tableName = 'channels';

    public function __construct($channelId)
    {
        parent::__construct();

        $sql  = "SELECT * FROM `$this->tableName` WHERE `id` = '" . $channelId . "';";

        $result = $this->getArrayAssoc($sql);

        $this->id        = $channelId;
        $this->name      = $result['name'];
        $this->type      = $result['type'];
        $this->class     = $result['class'];
        $this->smtp_host = $result['smtp_host'];
        $this->smtp_port = $result['smtp_port'];
        $this->smtp_user = $result['smtp_user'];
        $this->smtp_pass = $result['smtp_pass'];
    }
    //--------------------------------------------------------------------------


    public static function getTypeById($channelId)
    {
        $db = new Database;

        $sql = "SELECT `type` FROM `" . self::tableName. "` WHERE `id` = '" . $channelId . "' LIMIT 1;";
        $result = $db->getUpperLeft($sql);

        return $result;
    }
    //--------------------------------------------------------------------------


    public static function getClassById($channelId)
    {
        $db = new Database;

        $sql = "SELECT `class` FROM `" . self::tableName. "` WHERE `id` = '" . $channelId . "' LIMIT 1;";
        $result = $db->getUpperLeft($sql);

        return $result;
    }
    //--------------------------------------------------------------------------


    public static function getSmtpHostById($channelId)
    {
        $db = new Database;

        $sql = "SELECT `smtp_host` FROM `" . self::tableName. "` WHERE `id` = '" . $channelId . "' LIMIT 1;";
        $result = $db->getUpperLeft($sql);

        return $result;
    }
    //--------------------------------------------------------------------------


    public static function getSmtpPortById($channelId)
    {
        $db = new Database;

        $sql = "SELECT `smtp_port` FROM `" . self::tableName. "` WHERE `id` = '" . $channelId . "' LIMIT 1;";
        $result = $db->getUpperLeft($sql);

        return $result;
    }
    //--------------------------------------------------------------------------


    public static function getSmtpUserById($channelId)
    {
        $db = new Database;

        $sql = "SELECT `smtp_user` FROM `" . self::tableName. "` WHERE `id` = '" . $channelId . "' LIMIT 1;";
        $result = $db->getUpperLeft($sql);

        return $result;
    }
    //--------------------------------------------------------------------------


    public static function getSmtpPassById($channelId)
    {
        $db = new Database;

        $sql = "SELECT `smtp_pass` FROM `" . self::tableName. "` WHERE `id` = '" . $channelId . "' LIMIT 1;";
        $result = $db->getUpperLeft($sql);

        return $result;
    }
    //--------------------------------------------------------------------------


    public static function getNameById($channelId)
    {
        $db = new Database;

        $sql = "SELECT `name` FROM `" . self::tableName. "` WHERE `id` = '" . $channelId . "' LIMIT 1;";
        $result = $db->getUpperLeft($sql);

        return $result;
    }
    //--------------------------------------------------------------------------


    public function getName()
    {
        return $this->name;
    }
    //--------------------------------------------------------------------------


    public function getType()
    {
        return $this->type;
    }
    //--------------------------------------------------------------------------


    public function getClass()
    {
        return $this->class;
    }
    //--------------------------------------------------------------------------


    public function getSmtpHost()
    {
        return $this->smtp_host;
    }
    //--------------------------------------------------------------------------


    public function getSmtpPort()
    {
        return $this->smtp_port;
    }
    //--------------------------------------------------------------------------


    public function getSmtpUser()
    {
        return $this->smtp_user;
    }
    //--------------------------------------------------------------------------


    public function getSmtpPass()
    {
        return $this->smtp_pass;
    }
    //--------------------------------------------------------------------------
    
    
    public static function getAllChannels()
    {
        $db = new Database;

        $sql = "SELECT `id` FROM `" . self::tableName . "`;";
        $queryResult = $db->getArray($sql);

        $result = array();
        
        if (!empty($queryResult)) {
            foreach ($queryResult as $record) {
                $result[] = $record['id'];
            }
        }
        
        return $result;
    }
    //--------------------------------------------------------------------------
}