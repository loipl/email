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
    
    
    public static function getThrottlesByDomain($domain, $channel)
    {
        $db = new Database;

        $sql  = "SELECT `type` FROM `" . self::tableName . "`";
        $sql .= " WHERE `domain`            = '" . mysql_real_escape_string($domain). "'";
        $sql .= " AND   `channel`           = '" . mysql_real_escape_string($channel). "'";
        $sql .= " ;";

        $result = $db->getArray($sql);
        
        return $result;
    }
    //--------------------------------------------------------------------------
    
    
    public static function getThrottlesBySourceCampaign($sourceCampaign, $channel)
    {
        $db = new Database;

        $sql  = "SELECT `type` FROM `" . self::tableName . "`";
        $sql .= " WHERE `source_campaign`            = '" . mysql_real_escape_string($sourceCampaign). "'";
        $sql .= " AND   `channel`           = '" . mysql_real_escape_string($channel). "'";
        $sql .= " ;";

        $result = $db->getArray($sql);
        
        return $result;
    }
    //--------------------------------------------------------------------------
    
    
    public static function addThrottle($data)
    {
        $db = new Database;

        $sql  = 'INSERT INTO `' . self::tableName . '` (id, created, type, domain, source_campaign, channel, campaign_id, creative_id,';
        $sql .= 'category_id) VALUES (';
        $sql .= 'NULL,';
        $sql .= ' NOW(),';
        $sql .= ' \'' . mysql_real_escape_string($data['type']) . '\',';
        $sql .= ' \'' . mysql_real_escape_string($data['domain']) . '\',';
        $sql .= ' \'' . mysql_real_escape_string($data['source_campaign']) . '\',';
        $sql .= ' \'' . mysql_real_escape_string($data['channel']) . '\',';
        $sql .= ' \'' . mysql_real_escape_string($data['campaign_id']) . '\',';
        $sql .= ' \'' . mysql_real_escape_string($data['creative_id']) . '\',';
        $sql .= ' \'' . mysql_real_escape_string($data['category_id']) . '\'';
        $sql .= ')';

        $db->query($sql);

        return true;
    }
    //--------------------------------------------------------------------------
    
    
    public static function purgeThrottle()
    {
        $throttles = Throttle::getAllThrottles();
        
        if ( ! empty($throttles)) {
            foreach ($throttles as $record) {
                $id = $record['id'];
                $created = $record['created'];

                if ( (time() - strtotime($created)) > Config::PURGE_THROTTLE_THRESHOLD * 60 ) {
                    Throttle::removeRecord($id);
                }
            }
        }
    }
    //--------------------------------------------------------------------------
    
    
    public static function getAllThrottles()
    {
        $db = new Database;
        
        $sql  = "SELECT * FROM ".self::tableName;
        
        $result = $db->getArray($sql);
        
        return $result;
    }
    //--------------------------------------------------------------------------
    
    
    public static function removeRecord($id)
    {
        $db = new Database;

        $sql = "DELETE FROM `" . self::tableName . "` WHERE `id` = '" . mysql_real_escape_string($id) . "' LIMIT 1;";

        $db->query($sql);

        return true;
    }
    //--------------------------------------------------------------------------
    
    
    public static function getThrottledChannelsByCreativeId($creativeId) 
    {
        $db = new Database;

        $sql  = "SELECT `channel` FROM `" . self::tableName . "`";
        $sql .= " WHERE `creative_id`            = '" . mysql_real_escape_string($creativeId). "'";
        $sql .= " ;";

        $queryResult = $db->getArray($sql);
        $result = array();
        
        if (!empty ($queryResult)) {
            foreach ($queryResult as $record) {
                $result[] = $record['channel'];
            }
        }
        
        return array_unique($result);
    }
    //--------------------------------------------------------------------------
    
    
    public static function getThrottledChannelsByCategoryId($categoryId) 
    {
        $db = new Database;

        $sql  = "SELECT `channel` FROM `" . self::tableName . "`";
        $sql .= " WHERE `category_id`            = '" . mysql_real_escape_string($categoryId). "'";
        $sql .= " ;";

        $queryResult = $db->getArray($sql);
        $result = array();
        
        if (!empty ($queryResult)) {
            foreach ($queryResult as $record) {
                $result[] = $record['channel'];
            }
        }
        
        return array_unique($result);
    }
    //--------------------------------------------------------------------------
}