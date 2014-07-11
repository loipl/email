<?php

class Queue_Send extends Database
{

    protected $id;
    protected $created;
    protected $locked;
    protected $email;
    protected $campaignId;
    protected $creativeId;
    protected $categoryId;
    protected $from;
    protected $senderEmail;
    protected $subject;
    protected $htmlBody;
    protected $textBody;
    protected $subId;
    protected $channelId;
    protected $delayUntil;

    protected $tableName = 'queue_send';
    const      tableName = 'queue_send';

    public function __construct($id)
    {
        parent::__construct();

        $sql  = "SELECT * FROM `$this->tableName` WHERE `id` = '" . $id . "';";

        $result = $this->getArrayAssoc($sql);

        $this->id          = $id;
        $this->created     = $result['created'];
        $this->locked      = $result['locked'];
        $this->email       = $result['email'];
        $this->campaignId  = $result['campaign_id'];
        $this->creativeId  = $result['creative_id'];
        $this->categoryId  = $result['category_id'];
        $this->from        = $result['from_name'];
        $this->senderEmail = $result['sender_email'];
        $this->subject     = $result['subject'];
        $this->htmlBody    = $result['html_body'];
        $this->textBody    = $result['text_body'];
        $this->subId       = $result['sub_id'];
        $this->channelId   = $result['channel'];
        $this->delayUntil  = $result['delay_until'];
    }
    //--------------------------------------------------------------------------


    public static function getUnlockedRowIds($limit)
    {
        $db = new Database;

        $sql  = "SELECT `id`, `delay_until` FROM `" . self::tableName . "`";
        $sql .= " WHERE `locked` = '0'";
        $sql .= " ORDER BY RAND()";
        $sql .= " LIMIT " . mysql_real_escape_string($limit) . "";

        $result = $db->getArray($sql);

        if (!empty($result)) {
            // remove throtted rows
            foreach ($result as $index => $row) {
                if (! is_null($row['delay_until']) && intval($row['delay_until']) > 0) {
                    if (time() < strtotime($row['delay_until'])) {
                        unset($result[$index]);
                    }
                }
            }
        }

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


    public function getFromName()
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


    public function getChannelId()
    {
        return $this->channelId;
    }
    //--------------------------------------------------------------------------
    
    
    public function getDelayUntil()
    {
        return $this->delayUntil;
    }
    //--------------------------------------------------------------------------


    public static function getCreatedById($id)
    {
        $db = new Database;

        $sql = "SELECT `created` FROM `" . self::tableName. "` WHERE `id` = '" . $id . "' LIMIT 1;";
        $result = $db->getUpperLeft($sql);

        return $result;
    }
    //--------------------------------------------------------------------------


    public static function getLockedById($id)
    {
        $db = new Database;

        $sql = "SELECT `locked` FROM `" . self::tableName. "` WHERE `id` = '" . $id . "' LIMIT 1;";
        $result = $db->getUpperLeft($sql);

        return $result;
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


    public static function getCampaignIdById($id)
    {
        $db = new Database;

        $sql = "SELECT `campaign_id` FROM `" . self::tableName. "` WHERE `id` = '" . $id . "' LIMIT 1;";
        $result = $db->getUpperLeft($sql);

        return $result;
    }
    //--------------------------------------------------------------------------


    public static function getFromNameById($id)
    {
        $db = new Database;

        $sql = "SELECT `from_name` FROM `" . self::tableName. "` WHERE `id` = '" . $id . "' LIMIT 1;";
        $result = $db->getUpperLeft($sql);

        return $result;
    }
    //--------------------------------------------------------------------------


    public static function getSenderEmailById($id)
    {
        $db = new Database;

        $sql = "SELECT `sender_email` FROM `" . self::tableName. "` WHERE `id` = '" . $id . "' LIMIT 1;";
        $result = $db->getUpperLeft($sql);

        return $result;
    }
    //--------------------------------------------------------------------------


    public static function getSubjectById($id)
    {
        $db = new Database;

        $sql = "SELECT `subject` FROM `" . self::tableName. "` WHERE `id` = '" . $id . "' LIMIT 1;";
        $result = $db->getUpperLeft($sql);

        return $result;
    }
    //--------------------------------------------------------------------------


    public static function getHtmlBodyById($id)
    {
        $db = new Database;

        $sql = "SELECT `html_body` FROM `" . self::tableName. "` WHERE `id` = '" . $id . "' LIMIT 1;";
        $result = $db->getUpperLeft($sql);

        return $result;
    }
    //--------------------------------------------------------------------------


    public static function getHtmlTextById($id)
    {
        $db = new Database;

        $sql = "SELECT `html_text` FROM `" . self::tableName. "` WHERE `id` = '" . $id . "' LIMIT 1;";
        $result = $db->getUpperLeft($sql);

        return $result;
    }
    //--------------------------------------------------------------------------


    public static function getSubIdById($id)
    {
        $db = new Database;

        $sql = "SELECT `sub_id` FROM `" . self::tableName. "` WHERE `id` = '" . $id . "' LIMIT 1;";
        $result = $db->getUpperLeft($sql);

        return $result;
    }
    //--------------------------------------------------------------------------


    public static function getChannelIdById($id)
    {
        $db = new Database;

        $sql = "SELECT `channel` FROM `" . self::tableName. "` WHERE `id` = '" . $id . "' LIMIT 1;";
        $result = $db->getUpperLeft($sql);

        return $result;
    }
    //--------------------------------------------------------------------------


    public static function addRecord($email, $from, $campaignId, $creativeId, $categoryId, $senderEmail, $subject, $htmlBody, $textBody, $subId, $channel, $delayUntil)
    {
        $db = new Database;

        $sql  = "INSERT INTO `" . self::tableName . "` (id, created, locked, email, from_name, campaign_id,";
        $sql .= " creative_id, category_id, sender_email, subject, html_body, text_body, sub_id, channel, delay_until) VALUES (NULL, ";
        $sql .= " NOW(),";
        $sql .= " '0',";
        $sql .= " '" . mysql_real_escape_string($email). "',";
        $sql .= " '" . mysql_real_escape_string($from). "',";
        $sql .= " '" . mysql_real_escape_string($campaignId). "',";
        $sql .= " '" . mysql_real_escape_string($creativeId). "',";
        $sql .= " '" . mysql_real_escape_string($categoryId). "',";
        $sql .= " '" . mysql_real_escape_string($senderEmail). "',";
        $sql .= " '" . mysql_real_escape_string($subject). "',";
        $sql .= " '" . mysql_real_escape_string($htmlBody). "',";
        $sql .= " '" . mysql_real_escape_string($textBody). "',";
        $sql .= " '" . mysql_real_escape_string($subId). "',";
        $sql .= " '" . mysql_real_escape_string($channel). "',";
        $sql .= " '" . $delayUntil. "')";
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


    public function setLock()
    {
        $db = new Database;

        $sql = "UPDATE `" . self::tableName . "` SET `locked` = '1' WHERE `id` = '" . mysql_real_escape_string($this->id) . "' LIMIT 1;";
        $db->query($sql);

        return true;
    }
    //--------------------------------------------------------------------------


    public function removeLock()
    {
        $db = new Database;

        $sql = "UPDATE `" . self::tableName . "` SET `locked` = '0' WHERE `id` = '" . mysql_real_escape_string($this->id) . "' LIMIT 1;";
        $db->query($sql);

        return true;
    }
    //--------------------------------------------------------------------------


    public static function setLockById($id)
    {
        $db = new Database;

        $sql = "UPDATE `" . self::tableName . "` SET `locked` = '1' WHERE `id` = '" . mysql_real_escape_string($id) . "' LIMIT 1;";
        $db->query($sql);

        return true;
    }
    //--------------------------------------------------------------------------


    public static function removeLockById($id)
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
}