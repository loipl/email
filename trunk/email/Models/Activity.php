<?php

class Activity extends Database
{

    protected $id;
    protected $email;
    protected $datetime;
    protected $campaignId;
    protected $creativeId;
    protected $categoryId;
    protected $sender;
    protected $channel;

    protected $tableName = 'activity';
    const      tableName = 'activity';

    public function __construct($id)
    {
        parent::__construct();

        $sql  = "SELECT * FROM `$this->tableName` WHERE `id` = '" . $id . "';";

        $result = $this->getArrayAssoc($sql);

        $this->id          = $id;
        $this->email       = $result['email'];
        $this->datetime    = $result['datetime'];
        $this->campaign_id = $result['campaign_id'];
        $this->creative_id = $result['creative_id'];
        $this->category_id = $result['category_id'];
        $this->sender      = $result['sender'];
        $this->channel     = $result['channel'];
    }
    //--------------------------------------------------------------------------


    public static function getEmailById($id)
    {
        $db = new Database;

        $sql = "SELECT `email` FROM `" . self::tableName. "` WHERE `id` = '" . $id . "' LIMIT 1;";
        $result = $db->getUpperLeft($sql);

        return $result;
    }
    //--------------------------------------------------------------------------


    public static function getDatetimeById($id)
    {
        $db = new Database;

        $sql = "SELECT `datetime` FROM `" . self::tableName. "` WHERE `id` = '" . $id . "' LIMIT 1;";
        $result = $db->getUpperLeft($sql);

        return $result;
    }
    //--------------------------------------------------------------------------


    public static function getCampaignIdById($id)
    {
        $db = new Database;

        $sql = "SELECT `campaign_id` FROM `" . self::tableName. "` WHERE `id` = '" . $id . "' LIMIT 1;";
        $result = $db->getUpperLeft($sql);

        return $result;
    }
    //--------------------------------------------------------------------------


    public static function getCreativeIdById($id)
    {
        $db = new Database;

        $sql = "SELECT `creative_id` FROM `" . self::tableName. "` WHERE `id` = '" . $id . "' LIMIT 1;";
        $result = $db->getUpperLeft($sql);

        return $result;
    }
    //--------------------------------------------------------------------------


    public static function getCategoryIdById($id)
    {
        $db = new Database;

        $sql = "SELECT `category_id` FROM `" . self::tableName. "` WHERE `id` = '" . $id . "' LIMIT 1;";
        $result = $db->getUpperLeft($sql);

        return $result;
    }
    //--------------------------------------------------------------------------


    public static function getSenderById($id)
    {
        $db = new Database;

        $sql = "SELECT `sender` FROM `" . self::tableName. "` WHERE `id` = '" . $id . "' LIMIT 1;";
        $result = $db->getUpperLeft($sql);

        return $result;
    }
    //--------------------------------------------------------------------------


    public static function getChannelById($id)
    {
        $db = new Database;

        $sql = "SELECT `channel` FROM `" . self::tableName. "` WHERE `id` = '" . $id . "' LIMIT 1;";
        $result = $db->getUpperLeft($sql);

        return $result;
    }
    //--------------------------------------------------------------------------
    
    public static function getTrackingIdById($id)
    {
        $db = new Database;

        $sql = "SELECT `tracking_id` FROM `" . self::tableName. "` WHERE `id` = '" . $id . "' LIMIT 1;";
        $result = $db->getUpperLeft($sql);

        return $result;
    }
    //--------------------------------------------------------------------------
    
    public static function getActivityIdFromTrackingId($trackingId)
    {
        $db = new Database;

        $sql = "SELECT `id` FROM `" . self::tableName. "` WHERE `tracking_id` = '" . $trackingId . "' LIMIT 1;";
        $result = $db->getUpperLeft($sql);

        return $result;
    }
    //--------------------------------------------------------------------------


    public static function addActivity($email, $campaignId, $creativeId = NULL, $sender = NULL, $channel = NULL, $categoryId = NULL, $trackingId = Null)
    {
        $db = new Database;

        $sql  = "INSERT INTO `" . self::tableName . "` (email, datetime";

        if (!empty($campaignId)) {
            $sql .= ", campaign_id";
        }

        if (!empty($creativeId)) {
            $sql .= ", creative_id";
        }

        if (!empty($sender)) {
            $sql .= ", sender_id";
        }

        if (!empty($channel)) {
            $sql .= ", channel_id";
        }

        if (!empty($categoryId)) {
            $sql .= ", category_id";
        }
        
        if (!empty($trackingId)) {
            $sql .= ", tracking_id";
        }

        $sql .= ") VALUES (";
        $sql .= "'" . mysql_real_escape_string($email) . "', NOW()";

        if (!empty($campaignId)) {
            $sql .= ", '" . mysql_real_escape_string($campaignId) . "'";
        }

        if (!empty($creativeId)) {
            $sql .= ", '" . mysql_real_escape_string($creativeId) . "'";
        }

        if (!empty($sender)) {
            $sql .= ", '" . mysql_real_escape_string($sender) . "'";
        }

        if (!empty($channel)) {
            $sql .= ", '" . mysql_real_escape_string($channel) . "'";
        }

        if (!empty($categoryId)) {
            $sql .= ", '" . $categoryId . "'";
        }
        
        if (!empty($trackingId)) {
            $sql .= ", '" . $trackingId. "'";
        }

        $sql .= ");";

        $db->query($sql);
        
        return mysql_insert_id();
    }
    //--------------------------------------------------------------------------


    public static function addSendProcessData($subId, $channelId, $creativeId, $categoryId, $senderEmail)
    {
        $db = new Database;

        $sql  = "UPDATE `" . self::tableName . "` SET";
        $sql .= " `channel`      = '" . mysql_real_escape_string($channelId) . "'";
        $sql .= ", `creative_id` = '" . mysql_real_escape_string($creativeId) . "'";
        $sql .= ", `category_id` = '" . mysql_real_escape_string($categoryId) . "'";
        $sql .= ", `sender`      = '" . mysql_real_escape_string($senderEmail) . "'";
        $sql .= " WHERE `id`     = '" . mysql_real_escape_string($subId) . "'";
        $sql .= " LIMIT 1;";

        $db->query($sql);

        return mysql_insert_id();
    }
    //--------------------------------------------------------------------------

    public static function logActivity($email, $campaignId, $creativeId = NULL, $sender = NULL, $channel = NULL, $categoryId = NULL)
    {
        $db = new Database;

        $sql  = "INSERT INTO `" . self::tableName . "` (email, datetime, campaign_id, creative_id, sender, channel";
        if (!empty($categoryId)) {
            $sql .= ", category_id";
        }
        $sql .= ") VALUES (";
        $sql .= "'" . mysql_real_escape_string($email) . "', NOW(), '" . $campaignId . "', '" . $creativeId . "', '" . $sender . "', '" . $channel . "'";
        if (!empty($categoryId)) {
            $sql .= ", '" . $categoryId . "'";
        }
        $sql .= ");";

        $db->query($sql);

        return mysql_insert_id();
    }
    //--------------------------------------------------------------------------


    public static function removeActivity($subId)
    {
        $db = new Database;

        $sql = "DELETE FROM `" . self::tableName . "` WHERE `id` = '" . $subId . "' LIMIT 1;";

        $db->query($sql);

        return true;
    }
    //--------------------------------------------------------------------------
}