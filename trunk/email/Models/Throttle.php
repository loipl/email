<?php

class Throttle extends Database
{

    protected $id;
    protected $type;
    protected $domain;
    protected $channel;
    protected $creativeId;
    protected $campaignId;
    protected $categoryId;

    protected $tableName = 'throttles';
    const      tableName = 'throttles';

    public function __construct($id)
    {
        parent::__construct();

        $sql  = "SELECT * FROM `$this->tableName` WHERE `id` = '" . $id . "';";

        $result = $this->getArrayAssoc($sql);

        $this->id          = $id;
        $this->domain      = $result['domain'];
        $this->campaign_id = $result['campaign_id'];
        $this->creative_id = $result['creative_id'];
        $this->category_id = $result['category_id'];
        $this->channel     = $result['channel'];
    }
    //--------------------------------------------------------------------------

    public static function checkThrottleExists($data) 
    {
        $db = new Database;

        $sql  = "SELECT `id` FROM `" . self::tableName . "`";
        $sql .= " WHERE `type`              = '" . mysql_real_escape_string($data['type']). "'";
        $sql .= " AND   `domain`            = '" . mysql_real_escape_string($data['domain']) . "'";
        $sql .= " AND   `channel`           = '" . mysql_real_escape_string($data['channel']) . "'";
        $sql .= " AND   `campaign_id`       = '" . mysql_real_escape_string($data['campaign_id']) . "'";
        $sql .= " AND   `creative_id`       = '" . mysql_real_escape_string($data['creative_id']) . "'";
        $sql .= " AND   `category_id`       = '" . mysql_real_escape_string($data['category_id']) . "'";
        $sql .= " LIMIT 1;";

        $result = $db->getUpperLeft($sql);

        if ($result > 0) {
            return true;
        } else {
            return false;
        }
    }
    //--------------------------------------------------------------------------
    
    
    public static function getThrottleExistsType($domain, $channelId, $creativeId, $campaignId, $categoryId)
    {
        $db = new Database;

        $sql  = "SELECT `type` FROM `" . self::tableName . "`";
        $sql .= " WHERE `domain`            = '" . mysql_real_escape_string($domain). "'";
        $sql .= " AND   `channel`           = '" . mysql_real_escape_string($channelId) . "'";
        $sql .= " AND   `campaign_id`       = '" . mysql_real_escape_string($campaignId) . "'";
        $sql .= " AND   `creative_id`       = '" . mysql_real_escape_string($creativeId) . "'";
        $sql .= " AND   `category_id`       = '" . mysql_real_escape_string($categoryId) . "'";
        $sql .= " LIMIT 1;";

        $result = $db->getArray($sql);
        
        if ( ! empty($result)) {
            return (int) $result[0]['type'];
        } else {
            return false;
        }
    }
    //--------------------------------------------------------------------------
    
    
    public static function addThrottle($data)
    {
        $db = new Database;

        $sql  = 'INSERT INTO `' . self::tableName . '` (id, type, domain, channel, campaign_id, creative_id, category_id) VALUES (';
        $sql .= 'NULL,';
        $sql .= ' \'' . mysql_real_escape_string($data['type']) . '\',';
        $sql .= ' \'' . mysql_real_escape_string($data['domain']) . '\',';
        $sql .= ' \'' . mysql_real_escape_string($data['channel']) . '\',';
        $sql .= ' \'' . mysql_real_escape_string($data['campaign_id']) . '\',';
        $sql .= ' \'' . mysql_real_escape_string($data['creative_id']) . '\',';
        $sql .= ' \'' . mysql_real_escape_string($data['category_id']) . '\'';
        $sql .= ')';

        $db->query($sql);

        return true;
    }
    //--------------------------------------------------------------------------
    
    
    public static function removeAllRecords()
    {
        $db = new Database;

        $sql = "TRUNCATE `" . self::tableName;

        $db->query($sql);

        return true;
    }
    //--------------------------------------------------------------------------

}