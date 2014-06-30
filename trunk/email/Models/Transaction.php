<?php

class Transaction extends Database
{

    protected $id;
    protected $type;
    protected $email;
    protected $campaignId;
    protected $creativeId;
    protected $datetime;
    protected $activityId;

    protected $tableName = 'transactions';
    const      tableName = 'transactions';

    public function __construct($transactionId)
    {
        parent::__construct();

        $sql  = "SELECT * FROM `$this->tableName` WHERE `id` = '" . $transactionId . "';";

        $result = $this->getArrayAssoc($sql);

        $this->id         = $transactionId;
        $this->type       = $result['type'];
        $this->email      = $result['email'];
        $this->campaignId = $result['campaign_id'];
        $this->creativeId = $result['creative_id'];
        $this->datetime   = $result['datetime'];
        $this->activityId = $result['activity_id'];
    }
    //--------------------------------------------------------------------------


    public static function getTypeById($transactionID)
    {
        $db = new Database;

        $sql = "SELECT `type` FROM `" . self::tableName . "` WHERE `id` = '" . $transactionID . "' LIMIT 1;";
        $result = $db->getUpperLeft($sql);

        return $result;
    }
    //--------------------------------------------------------------------------


    public static function getEmailById($transactionID)
    {
        $db = new Database;

        $sql = "SELECT `email` FROM `" . self::tableName . "` WHERE `id` = '" . $transactionID . "' LIMIT 1;";
        $result = $db->getUpperLeft($sql);

        return $result;
    }
    //--------------------------------------------------------------------------


    public static function getCampaignIdById($transactionID)
    {
        $db = new Database;

        $sql = "SELECT `campaign_id` FROM `" . self::tableName . "` WHERE `id` = '" . $transactionID . "' LIMIT 1;";
        $result = $db->getUpperLeft($sql);

        return $result;
    }
    //--------------------------------------------------------------------------


    public static function getCreativeIdById($transactionID)
    {
        $db = new Database;

        $sql = "SELECT `creative_id` FROM `" . self::tableName . "` WHERE `id` = '" . $transactionID . "' LIMIT 1;";
        $result = $db->getUpperLeft($sql);

        return $result;
    }
    //--------------------------------------------------------------------------


    public static function getDatetimeById($transactionID)
    {
        $db = new Database;

        $sql = "SELECT `datetime` FROM `" . self::tableName . "` WHERE `id` = '" . $transactionID . "' LIMIT 1;";
        $result = $db->getUpperLeft($sql);

        return $result;
    }
    //--------------------------------------------------------------------------


    public static function getActivityIdById($transactionID)
    {
        $db = new Database;

        $sql = "SELECT `activity_id` FROM `" . self::tableName . "` WHERE `id` = '" . $transactionID . "' LIMIT 1;";
        $result = $db->getUpperLeft($sql);

        return $result;
    }
    //--------------------------------------------------------------------------


    public function getType()
    {
        return $this->type;
    }
    //--------------------------------------------------------------------------


    public function getEmail()
    {
        return trim($this->email);
    }
    //--------------------------------------------------------------------------


    public function getCampaignId()
    {
        return $this->campaignId;
    }
    //--------------------------------------------------------------------------


    public function getCreativeId()
    {
        return $this->creativeId;
    }
    //--------------------------------------------------------------------------


    public function getDatetime()
    {
        return $this->datetime;
    }
    //--------------------------------------------------------------------------


    public function getActivityId()
    {
        return $this->activityId;
    }
    //--------------------------------------------------------------------------


    public static function checkTransactionExists($type, $email, $activity_id)
    {
        $db = new Database;

        $sql  = "SELECT `id` FROM `" . self::tableName . "`";
        $sql .= " WHERE `type`        = '" . mysql_real_escape_string($type). "'";
        $sql .= " AND   `email`       = '" . mysql_real_escape_string($email) . "'";
        $sql .= " AND   `activity_id` = '" . mysql_real_escape_string($activity_id) . "'";
        $sql .= " LIMIT 1;";
        $result = $db->getUpperLeft($sql);

        if ($result > 0) {
            return true;
        } else {
            return false;
        }
    }
    //--------------------------------------------------------------------------


    public static function addTransaction($email, $type, $activity_id = NULL)
    {
        $db = new Database;

        $sql  = 'INSERT INTO `' . self::tableName . '` (id, type, email, campaign_id, creative_id, datetime, activity_id) VALUES (';
        $sql .= 'NULL,';
        $sql .= ' \'' . $type . '\',';
        $sql .= ' \'' . mysql_real_escape_string($email) . '\',';
        $sql .= ' NULL,';
        $sql .= ' NULL,';
        $sql .= ' NOW(), ';

        if (isset($activity_id) && is_numeric($activity_id)) {
            $sql .= ' \'' . $activity_id . '\'';
        } else {
        $sql .= ' NULL';
        }

        $sql .= ')';

        $db->query($sql);

        return true;
    }
    //--------------------------------------------------------------------------
}