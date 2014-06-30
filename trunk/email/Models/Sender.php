<?php

class Sender extends Database
{

    protected $id;
    protected $name;
    protected $domain;
    protected $footerId;

    protected $tableName = 'senders';
    const      tableName = 'senders';

    public function __construct($senderId)
    {
        parent::__construct();

        $sql  = "SELECT * FROM `$this->tableName` WHERE `id` = '" .$senderId. "';";

        $result = $this->getArrayAssoc($sql);

        $this->id       = $senderId;
        $this->name     = $result['name'];
        $this->domain   = $result['domain'];
        $this->footerId = $result['footer_id'];
    }
    //--------------------------------------------------------------------------


    public static function getNameById($senderID)
    {
        $db = new Database;

        $sql = "SELECT `name` FROM `" . self::tableName. "` WHERE `id` = '" . $senderID . "' LIMIT 1;";
        $result = $db->getUpperLeft($sql);

        return $result;
    }
    //--------------------------------------------------------------------------


    public static function getDomainById($senderID)
    {
        $db = new Database;

        $sql = "SELECT `domain` FROM `" . self::tableName. "` WHERE `id` = '" . $senderID . "' LIMIT 1;";
        $result = $db->getUpperLeft($sql);

        return $result;
    }
    //--------------------------------------------------------------------------


    public static function getFooterIdById($senderID)
    {
        $db = new Database;

        $sql = "SELECT `footer_id` FROM `" . self::tableName. "` WHERE `id` = '" . $senderID . "' LIMIT 1;";
        $result = $db->getUpperLeft($sql);

        return $result;
    }
    //--------------------------------------------------------------------------


    public function getName()
    {
        return $this->name;
    }
    //--------------------------------------------------------------------------


    public function getDomain()
    {
        return $this->domain;
    }
    //--------------------------------------------------------------------------


    public function getFooterId()
    {
        return $this->footerId;
    }
    //--------------------------------------------------------------------------
}