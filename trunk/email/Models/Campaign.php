<?php

class Campaign extends Database
{

    protected $id;
    protected $name;
    protected $attributes;
    protected $sendLimit;
    protected $creativeIds;
    protected $endDate;
    protected $sentCount;

    protected $tableName = 'campaigns';
    const      tableName = 'campaigns';

    public function __construct($campaignId)
    {
        parent::__construct();

        $sql  = "SELECT * FROM `$this->tableName` WHERE `id` = '" . $campaignId . "';";

        $result = $this->getArrayAssoc($sql);

        $this->id          = $campaignId;
        $this->name        = $result['name'];
        $this->attributes  = $result['attributes'];
        $this->sendLimit   = $result['send_limit'];
        $this->creativeIds = $result['creative_ids'];
        $this->endDate     = $result['end_date'];
        $this->sentCount   = $result['sent_count'];
    }
    //--------------------------------------------------------------------------


    public static function getRandomCampaign()
    {
        $db = new Database;

        $sql =  "SELECT `id` FROM `" . self::tableName . "`";
        $sql .= " WHERE `end_date` > NOW()";
        $sql .= " AND `sent_count` < `send_limit`";
        $sql .= " ORDER BY RAND() LIMIT 1;";

        $result = $db->getUpperLeft($sql);

        return $result;
    }
    //--------------------------------------------------------------------------


    public static function getEligibleCampaigns()
    {
        $db = new Database;

        $sql =  "SELECT `id` FROM `" . self::tableName . "`";
        $sql .= " WHERE `end_date` > NOW()";
        $sql .= " AND (`sent_count` < `send_limit` OR `send_limit` = '0')";
        $sql .= " AND `locked` = '0'";
        $sql .= " AND `attributes` IS NOT NULL";

        $result = $db->getArray($sql);

        return $result;
    }
    //--------------------------------------------------------------------------


    public function getSuppressionList()
    {
        $db = new Database;

        $sql =  "SELECT `md5_email` FROM `suppression_campaigns`";
        $sql .= " WHERE `campaign_id` = '" . $this->id . "'";

        $result = $db->getArray($sql);

        if (!empty($result)) {
            foreach ($result AS $row) {
                $suppressionList[] = $row['md5_email'];
            }
        }

        if (isset($suppressionList) && !empty($suppressionList)) {
            return $suppressionList;
        } else {
            return false;
        }
    }
    //--------------------------------------------------------------------------


    public static function getNameById($campaignId)
    {
        $db = new Database;

        $sql = "SELECT `name` FROM `" . self::tableName . "` WHERE `id` = '" . $campaignId . "' LIMIT 1;";
        $result = $db->getUpperLeft($sql);

        return $result;
    }
    //--------------------------------------------------------------------------


    public static function getAttributesById($campaignId)
    {
        $db = new Database;

        $sql = "SELECT `attributes` FROM `" . self::tableName . "` WHERE `id` = '" . $campaignId . "' LIMIT 1;";
        $result = $db->getUpperLeft($sql);

        return $result;
    }
    //--------------------------------------------------------------------------


    public static function getSendLimitById($campaignId)
    {
        $db = new Database;

        $sql = "SELECT `send_limit` FROM `" . self::tableName . "` WHERE `id` = '" . $campaignId . "' LIMIT 1;";
        $result = $db->getUpperLeft($sql);

        return $result;
    }
    //--------------------------------------------------------------------------


    public static function getCreativeIdsById($campaignId)
    {
        $db = new Database;

        $sql = "SELECT `creative_ids` FROM `" . self::tableName . "` WHERE `id` = '" . $campaignId . "' LIMIT 1;";
        $result = $db->getUpperLeft($sql);

        return $result;
    }
    //--------------------------------------------------------------------------


    public static function getEndDateById($campaignId)
    {
        $db = new Database;

        $sql = "SELECT `end_date` FROM `" . self::tableName . "` WHERE `id` = '" . $campaignId . "' LIMIT 1;";
        $result = $db->getUpperLeft($sql);

        return $result;
    }
    //--------------------------------------------------------------------------


    public static function getSentCountById($campaignId)
    {
        $db = new Database;

        $sql = "SELECT `sent_count` FROM `" . self::tableName . "` WHERE `id` = '" . $campaignId . "' LIMIT 1;";
        $result = $db->getUpperLeft($sql);

        return $result;
    }
    //--------------------------------------------------------------------------


    public static function addSentCount($campaignId)
    {
        $db = new Database;

        $sql = "UPDATE `" . self::tableName . "` SET `sent_count` = (`sent_count` + 1) WHERE `id` = '" . $campaignId . "' LIMIT 1;";
        $db->query($sql);

        return true;
    }
    //--------------------------------------------------------------------------


    public function getId()
    {
        return $this->id;
    }
    //--------------------------------------------------------------------------


    public function getName()
    {
        return $this->name;
    }
    //--------------------------------------------------------------------------


    public function getAttributes()
    {
        return $this->attributes;
    }
    //--------------------------------------------------------------------------


    public function getSendLimit()
    {
        return $this->sendLimit;
    }
    //--------------------------------------------------------------------------


    public function getCreativeIds() {
        return $this->creativeIds;
    }
    //--------------------------------------------------------------------------


    public function getEndDate()
    {
        return $this->endDate;
    }
    //--------------------------------------------------------------------------


    public function getSentCount()
    {
        return $this->sentCount;
    }
    //--------------------------------------------------------------------------


    public static function setLock($campaignId)
    {
        $db = new Database;

        $sql = "UPDATE `" . self::tableName . "` SET `locked` = '1', `lock_datetime` = NOW() WHERE `id` = '" . mysql_real_escape_string($campaignId) . "' LIMIT 1;";
        $db->query($sql);

        return true;
    }
    //--------------------------------------------------------------------------


    public static function removeLock($campaignId)
    {
        $db = new Database;

        $sql = "UPDATE `" . self::tableName . "` SET `locked` = '0', `lock_datetime` = NULL WHERE `id` = '" . mysql_real_escape_string($campaignId) . "' LIMIT 1;";
        $db->query($sql);

        return true;
    }
    //--------------------------------------------------------------------------


    public static function getCampaignsWithLocks($count = 10000, $interval = Config::CAMPAIGN_TIMEOUT)
    {
        $db = new Database;

        $currentDate = new Datetime();
        $currentDate->sub(new Dateinterval('PT' . $interval . 'S'));
        $cutOff      = $currentDate->format('Y-m-d H:i:s');

        $sql  = "SELECT `id` FROM `" . self::tableName . "`";
        $sql .= " WHERE `locked` = '1' AND `lock_datetime` < '" . $cutOff . "'";
        $sql .= " LIMIT " . $count . "";

        $result = $db->getArray($sql);

        return $result;
    }
    //--------------------------------------------------------------------------
    
    
    public static function getAllCampaign()
    {
        $db = new Database;

        $sql  = "SELECT * FROM `" . self::tableName . "` ORDER BY `id` DESC LIMIT 20";

        $result = $db->getArray($sql);

        return $result;
    }
    //--------------------------------------------------------------------------
    
    public static function updateCampaignById($id, $updateData)
    {
        $db = new Database;
        
        $fields = array('name', 'attributes', 'send_limit', 'sent_count', 'creative_ids', 'end_date');
        foreach ($updateData as $key => $value) {
            if (!in_array($key, $fields)) {
                unset($updateData[$key]);
            }
        }

        $sql  = "UPDATE `" . self::tableName . "` SET";
        
        foreach ($updateData as $field => $data) {
            if (!empty($data)) {
                $sql .= " `$field` = '" . mysql_real_escape_string($data) . "',";
            } else {
                $sql .= " `$field` = null,";
            }
        }
        $sql = rtrim($sql, ',');

        $sql .= " WHERE `id` = '" . mysql_real_escape_string($id) . "'";
        $db->query($sql);

        return true;
    }
    
    //--------------------------------------------------------------------------
    public static function insertCampaign($name, $attributes, $sendLimit, $sentCount, $creativeIds, $endDate)
    {
        $db = new Database;

        $sql  = "INSERT INTO `" . self::tableName . "` " .
                "SET name = '" . mysql_real_escape_string($name) . "'" .
                ", attributes = '" . mysql_real_escape_string($attributes) . "'" .
                ", send_limit = '" . mysql_real_escape_string($sendLimit) . "'" .
                ", sent_count = '" . mysql_real_escape_string($sentCount) . "'" .
                ", creative_ids = '" . mysql_real_escape_string($creativeIds) . "'" .
                ", end_date = '" . mysql_real_escape_string($endDate) . "'";

        $db->query($sql);
        
        return mysql_insert_id();

    }
    //--------------------------------------------------------------------------
    
    public static function copyCampaign($campaignId)
    {
        $db = new Database;

        $sql  = "INSERT INTO `" . self::tableName . "` " .
                "(name, attributes, send_limit, sent_count, creative_ids, end_date)" .
                " SELECT CONCAT(name, ' - Copy') as name," .
                        " attributes, send_limit, sent_count, creative_ids, end_date" .
                " FROM `" . self::tableName . "` " .
                " WHERE id= '" . mysql_real_escape_string($campaignId) . "'";

        $db->query($sql);
        
        return mysql_insert_id();

    }
    //--------------------------------------------------------------------------
    
     public static function deleteCampaign($campaignId)
    {
        $db = new Database;

        $sql  = "DELETE FROM `" . self::tableName . "` " .
                " WHERE id= '" . mysql_real_escape_string($campaignId) . "'";

        $db->query($sql);
        
        return true;

    }
    //--------------------------------------------------------------------------
    
    public static function formatCampaign($campaigns) 
    {
        foreach ($campaigns as $index => $campaign) {
            $campaigns[$index]['attributes'] = unserialize($campaign['attributes']);
            $campaigns[$index]['creative_ids'] = unserialize($campaign['creative_ids']);
        }
        return $campaigns;
    }
}