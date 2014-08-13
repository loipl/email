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
    const      pageSize  = 50;

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
    
    
    public static function getThrottlesByDomain($throttles, $domain, $channel = null)
    {
        if (empty($throttles)) {
            return false;
        }
        
        $result = array();
        
        foreach ($throttles as $row) {
            $matched = true;
            
            if (mysql_real_escape_string($domain) !== $row['domain']) {
                $matched = false;
            }
            
            if (!empty($channel) && (mysql_real_escape_string($channel) !== $row['channel'])) {
                $matched = false;
            }
            
            if ($matched) {
                $result[] = $row['type'];
            }
        }
        
        return $result;
    }
    //--------------------------------------------------------------------------
    
    
    public static function getThrottlesByTldGroup($throttles, $tldGroup, $channel = null)
    {
        if (empty($throttles)) {
            return false;
        }
        
        $result = array();
        
        foreach ($throttles as $row) {
            $matched = true;
            
            if (mysql_real_escape_string($tldGroup) !== $row['tld_group']) {
                $matched = false;
            }
            
            if (!empty($channel) && (mysql_real_escape_string($channel) !== $row['channel'])) {
                $matched = false;
            }
            
            if ($matched) {
                $result[] = $row['type'];
            }
        }

        return $result;
    }
    //--------------------------------------------------------------------------
    
    
    public static function getThrottlesBySourceCampaign($throttles, $sourceCampaign, $channel = null)
    {
        if (empty($throttles)) {
            return false;
        }
        
        $result = array();
        
        foreach ($throttles as $row) {
            $matched = true;
            
            if (mysql_real_escape_string($sourceCampaign) !== $row['source_campaign']) {
                $matched = false;
            }
            
            if (!empty($channel) && (mysql_real_escape_string($channel) !== $row['channel'])) {
                $matched = false;
            }
            
            if ($matched) {
                $result[] = $row['type'];
            }
        }

        return $result;
    }
    //--------------------------------------------------------------------------
    
    
    public static function addThrottle($data)
    {
        $db = new Database;

        $sql  = 'INSERT INTO `' . self::tableName . '` (id, created, type, domain, source_campaign, tld_group, channel, campaign_id, creative_id,';
        $sql .= 'category_id) VALUES (';
        $sql .= 'NULL,';
        $sql .= ' NOW(),';
        $sql .= ' \'' . mysql_real_escape_string($data['type']) . '\',';
        $sql .= ' \'' . mysql_real_escape_string($data['domain']) . '\',';
        $sql .= ' \'' . mysql_real_escape_string($data['source_campaign']) . '\',';
        $sql .= ' \'' . mysql_real_escape_string($data['tld_group']) . '\',';
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
    // -------------------------------------------------------------------------
    
    public static function getAll($start = null, $end = null, $page = '1', $sortBy = null, $sortOrder = null) 
    {
            
        $sql = "SELECT * FROM `". self::tableName ."`";
        
        $wheres = array();
        if (!empty($start)) {
            $start = mysql_real_escape_string($start . ' 00:00:00');
            $wheres[] = " `created` >= '$start' ";
        }
        
        if (!empty($end)) {
            $end = mysql_real_escape_string($end . ' 23:59:59');
            $wheres[] = " `created` <= '$end' ";
        }
        
        if (!empty($wheres)) {
            $sql .= " WHERE" . implode('AND', $wheres);
        }
        
        if (!empty($sortBy)) {
            $sortBy = mysql_real_escape_string($sortBy);
            $sortOrder = strtoupper($sortOrder) === 'ASC' ? 'ASC' : 'DESC';
            $sql .= " ORDER BY `$sortBy` $sortOrder";
        }
        
        $page = intval($page) > 0 ? intval($page) : 1;
        $pageSize = self::pageSize;
        $start = ($page - 1) * $pageSize;
        $sql .= " LIMIT $start,$pageSize;";
        $db = new Database;   
        return $db->getArray($sql); 
    }
    //--------------------------------------------------------------------------
    
    public static function countAll($start = null, $end = null) 
    {
            
        $sql = "SELECT count(*) as count FROM `". self::tableName ."`";    
        
        $wheres = array();
        if (!empty($start)) {
            $start = mysql_real_escape_string($start . ' 00:00:00');
            $wheres[] = " `created` >= '$start' ";
        }
        
        if (!empty($end)) {
            $end = mysql_real_escape_string($end . ' 23:59:59');
            $wheres[] = " `created` <= '$end' ";
        }
        
        if (!empty($wheres)) {
            $sql .= " WHERE" . implode('AND', $wheres);
        }
        
        $db = new Database;   
        $dbData = $db->getArray($sql); 
        if (empty($dbData)) {
            return 0;
        } else {
            return intval($dbData[0]['count']);
        }
    }
    // -------------------------------------------------------------------------
}