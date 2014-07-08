<?php

class Queue_Build extends Database
{

    protected $id;
    protected $created;
    protected $locked;
    protected $email;
    protected $stage;
    protected $campaignId;
    protected $creativeId;
    protected $categoryId;
    protected $from;
    protected $senderEmail;
    protected $subject;
    protected $htmlBody;
    protected $textBody;
    protected $subId;
    protected $channel;

    protected $tableName = 'queue_build';
    const      tableName = 'queue_build';

    public function __construct($id)
    {
        parent::__construct();

        $sql  = "SELECT * FROM `$this->tableName` WHERE `id` = '" . $id . "';";

        $result = $this->getArrayAssoc($sql);

        $this->id          = $id;
        $this->created     = $result['created'];
        $this->locked      = $result['locked'];
        $this->email       = $result['email'];
        $this->stage       = $result['stage'];
        $this->campaignId  = $result['campaign_id'];
        $this->creativeId  = $result['creative_id'];
        $this->categoryId  = $result['category_id'];
        $this->from        = $result['from_name'];
        $this->senderEmail = $result['sender_email'];
        $this->subject     = $result['subject'];
        $this->htmlBody    = $result['html_body'];
        $this->textBody    = $result['text_body'];
        $this->subId       = $result['sub_id'];
        $this->channel     = $result['channel'];
    }
    //--------------------------------------------------------------------------


    public static function addRecord($email, $from = NULL, $senderEmail = NULL, $subject = NULL, $htmlBody = NULL, $textBody = NULL, $subId = NULL, $channel = NULL, $stage = '1')
    {
        $db = new Database;

        $sql  = "INSERT INTO `" . self::tableName . "` (id, created, locked, email, stage, from_name,";
        $sql .= " sender_email, subject, html_body, text_body, sub_id, channel) VALUES (NULL, ";
        $sql .= " NOW(),";
        $sql .= " '0',";
        $sql .= " '" . mysql_real_escape_string($email). "',";
        $sql .= " '" . mysql_real_escape_string($stage). "'";

        if (!empty($from)) {
            $sql .= ", '" . mysql_real_escape_string($from). "'";
        } else {
            $sql .= ", NULL";
        }

        if (!empty($senderEmail)) {
            $sql .= ", '" . mysql_real_escape_string($senderEmail). "'";
        } else {
            $sql .= ", NULL";
        }

        if (!empty($subject)) {
            $sql .= " '" . mysql_real_escape_string($subject). "'";
        } else {
            $sql .= ", NULL";
        }

        if (!empty($htmlBody)) {
            $sql .= " '" . mysql_real_escape_string($htmlBody). "'";
        } else {
            $sql .= ", NULL";
        }

        if (!empty($textBody)) {
            $sql .= " '" . mysql_real_escape_string($textBody). "'";
        } else {
            $sql .= ", NULL";
        }

        if (!empty($subId)) {
            $sql .= " '" . mysql_real_escape_string($subId). "'";
        } else {
            $sql .= ", NULL";
        }

        if (!empty($channel)) {
            $sql .= " '" . mysql_real_escape_string($channel). "'";
        } else {
            $sql .= ", NULL";
        }

        $sql .= ");";

        $db->query($sql);

        return true;
    }
    //--------------------------------------------------------------------------
    
    
    public static function addFullRecord($data) {
        $db = new Database;

        $sql  = "INSERT INTO `" . self::tableName . "` (id, created, locked, email, stage, campaign_id, creative_id, category_id, from_name,";
        $sql .= " sender_email, subject, html_body, text_body, sub_id, channel) VALUES (NULL, ";
        $sql .= " NOW(),";
        $sql .= " '0',";
        $sql .= " '" . mysql_real_escape_string($data['email']). "',";
        $sql .= " '" . mysql_real_escape_string($data['stage']). "',";
        $sql .= " '" . mysql_real_escape_string($data['campaign_id']). "',";
        $sql .= " '" . mysql_real_escape_string($data['creative_id']). "',";
        $sql .= " '" . mysql_real_escape_string($data['category_id']). "',";
        $sql .= " '" . mysql_real_escape_string($data['from_name']). "',";
        $sql .= " '" . mysql_real_escape_string($data['sender_email']). "',";
        $sql .= " '" . mysql_real_escape_string($data['subject']). "',";
        $sql .= " '" . mysql_real_escape_string($data['html_body']). "',";
        $sql .= " '" . mysql_real_escape_string($data['text_body']). "',";
        $sql .= " '" . mysql_real_escape_string($data['sub_id']). "',";
        $sql .= " '" . mysql_real_escape_string($data['channel']). "'";

        $sql .= ");";

        $db->query($sql);

        return true;
    }
    //--------------------------------------------------------------------------
    

    public static function addCreativeAndCampaignIds($id, $creativeId, $campaignId)
    {
        if (empty($id) || !is_numeric($id)) {
            return false;
        }

        $db = new Database;

        $sql  = "UPDATE `" . self::tableName . "` SET";

        $sql .= " `creative_id`   = '" . mysql_real_escape_string($creativeId) . "'";
        $sql .= ", `campaign_id`  = '" . mysql_real_escape_string($campaignId) . "'";
        $sql .= ", `stage`        = '2'";
        $sql .= " WHERE `id`      = '" . mysql_real_escape_string($id) . "';";

        $db->query($sql);

        return true;
    }
    //--------------------------------------------------------------------------


    public static function addCreativeData($id, $creativeData)
    {
        if (empty($id) || !is_numeric($id)) {
            return false;
        }

        $db = new Database;

        $sql  = "UPDATE `" . self::tableName . "` SET";

        $sql .= " `sender_email`  = '" . mysql_real_escape_string($creativeData['sender_email']) . "'";
        $sql .= ", `from_name`    = '" . mysql_real_escape_string($creativeData['from_name']) . "'";
        $sql .= ", `category_id`  = '" . mysql_real_escape_string($creativeData['category_id']) . "'";
        $sql .= ", `subject`      = '" . mysql_real_escape_string($creativeData['subject']) . "'";

        if (!empty($creativeData['html_body'])) {
            $sql .= ", `html_body` = ";
            $sql .= "'" . mysql_real_escape_string($creativeData['html_body']). "'";
        }

        if (!empty($creativeData['text_body'])) {
            $sql .= ", `text_body` = ";
            $sql .= "'" . mysql_real_escape_string($creativeData['text_body']). "'";
        }

        $sql .= ", `stage` = '3'";
        $sql .= " WHERE `id` = '" . mysql_real_escape_string($id) . "';";

        $db->query($sql);

        return true;
    }
    //--------------------------------------------------------------------------


    public static function addChannelData($id, $channelId)
    {
        if (empty($id) || !is_numeric($id)) {
            return false;
        }

        $db = new Database;

        $sql  = "UPDATE `" . self::tableName . "` SET";

        $sql .= " `channel` = '" . mysql_real_escape_string($channelId) . "'";
        $sql .= ", `stage` = '4'";
        $sql .= " WHERE `id` = '" . mysql_real_escape_string($id) . "';";

        $db->query($sql);

        return true;
    }
    //--------------------------------------------------------------------------


    public static function removeRecordById($id)
    {
        $db = new Database;

        $sql = "DELETE FROM `" . self::tableName . "` WHERE `id` = '" . mysql_real_escape_string($id) . "' LIMIT 1;";

        $db->query($sql);

        return true;
    }
    //--------------------------------------------------------------------------


    public function removeRecord()
    {
        $db = new Database;

        $sql = "DELETE FROM `" . self::tableName . "` WHERE `id` = '" . mysql_real_escape_string($this->id) . "' LIMIT 1;";

        $db->query($sql);

        return true;
    }
    //--------------------------------------------------------------------------
    

    public static function setLock($id)
    {
        $db = new Database;

        $sql = "UPDATE `" . self::tableName . "` SET `locked` = '1' WHERE `id` = '" . mysql_real_escape_string($id) . "' LIMIT 1;";
        $db->query($sql);

        return true;
    }
    //--------------------------------------------------------------------------


    public static function removeLock($id)
    {
        $db = new Database;

        $sql = "UPDATE `" . self::tableName . "` SET `locked` = '0' WHERE `id` = '" . mysql_real_escape_string($id) . "' LIMIT 1;";
        $db->query($sql);

        return true;
    }
    //--------------------------------------------------------------------------


    public static function getIdsWithLocks()
    {
        $db = new Database;

        $sql  = "SELECT `id` FROM `" . self::tableName . "`";
        $sql .= " WHERE `locked` = '1'";

        $result = $db->getArray($sql);

        return $result;
    }
    //--------------------------------------------------------------------------


    public static function getBlankRecords($leads)
    {
        foreach($leads AS $lead) {
            $queueIds[] = $lead['build_queue_id'];
        }

        $idList = implode(',', $queueIds);

        $db = new Database;

        $sql  = "SELECT `id`,`email`,`sub_id` FROM `" . self::tableName . "`";
        $sql .= " WHERE `id` IN (" . $idList . ")";
        $sql .= " AND `category_id` IS NULL";
        $sql .= " AND `html_body`   IS NULL";
        $sql .= " AND `text_body`   IS NULL";

        $result = $db->getArray($sql);

        return $result;
    }
    //--------------------------------------------------------------------------


    public static function isLocked($id)
    {
        $db = new Database;

        $sql = "SELECT `locked` FROM `" . self::tableName . "` WHERE `id` = '" . mysql_real_escape_string($id) . "' LIMIT 1;";
        $result = $db->getUpperLeft($sql);

        if ($result == 1) {
            return true;
        } else {
            return false;
        }
    }
    //--------------------------------------------------------------------------


    public static function getStageById($id)
    {
        $db = new Database;

        $sql = "SELECT `stage` FROM `" . self::tableName . "` WHERE `id` = '" . mysql_real_escape_string($id) . "' LIMIT 1;";
        $result = $db->getUpperLeft($sql);

        return $result;
    }
    //--------------------------------------------------------------------------


    public static function updateStage($id, $stage)
    {
        if (empty($stage) || !is_numeric($stage)) {
            return false;
        }

        $db = new Database;

        $sql = "UPDATE `" . self::tableName . "` SET `stage` = '" . mysql_real_escape_string($stage) . "' WHERE `id` = '" . mysql_real_escape_string($id) . "' LIMIT 1;";
        $result = $db->query($sql);

        return $result;
    }
    //--------------------------------------------------------------------------


    public static function addSubId($id, $subId)
    {
        if (empty($subId) || !is_numeric($subId)) {
            return false;
        }

        $db = new Database;

        $sql = "UPDATE `" . self::tableName . "` SET `sub_id` = '" . mysql_real_escape_string($subId) . "' WHERE `id` = '" . mysql_real_escape_string($id) . "' LIMIT 1;";
        $result = $db->query($sql);

        return $result;
    }
    //--------------------------------------------------------------------------


    public function getCreated()
    {
        return $this->created;
    }
    //--------------------------------------------------------------------------


    public function getLocked()
    {
        return $this->locked;
    }
    //--------------------------------------------------------------------------


    public function getEmail()
    {
        return $this->email;
    }
    //--------------------------------------------------------------------------


    public function getStage()
    {
        return $this->stage;
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


    public function getCategoryId()
    {
        return $this->categoryId;
    }
    //--------------------------------------------------------------------------


    public function getFrom()
    {
        return $this->from;
    }
    //--------------------------------------------------------------------------


    public function getSenderEmail()
    {
        return $this->senderEmail;
    }
    //--------------------------------------------------------------------------


    public function getSubject()
    {
        return $this->subject;
    }
    //--------------------------------------------------------------------------


    public function getHtmlBody()
    {
        return $this->htmlBody;
    }
    //--------------------------------------------------------------------------


    public function getTextBody()
    {
        return $this->textBody;
    }
    //--------------------------------------------------------------------------


    public function getSubId()
    {
        return $this->subId;
    }
    //--------------------------------------------------------------------------


    public function getChannel()
    {
        return $this->channel;
    }
    //--------------------------------------------------------------------------
}