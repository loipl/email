<?php

class LogScheduler extends Database
{

    // id of processing record
    protected static $id = null;
    
    // array (table_column => column_value)
    protected static $attributes = array();
    
    const tableName = 'log_scheduler';
    const pageSize = 20;
    const leadLogLimit = 1000;
    protected static $tableFields = array (
                                        'id', 
                                        'scheduler_name', 
                                        'eligible_campaign_count', 
                                        'eligible_campaign_ids', 
                                        'chosen_campaign_id',
                                        'chosen_campaign_attribute',
                                        'lead_count',
                                        'leads',
                                        'queued_lead_count',
                                        'message',
                                        'create_time'
                                    );
            
    
    public static function init($id = null)
    {
        
        if (is_null($id)) {
            $db = new Database;
            $sql  = "INSERT INTO `". self::tableName ."`() VALUES ();";
            $db->query($sql);
            self::$id = mysql_insert_id();
        } else {
            self::$id = $id;
        }
        
        self::$attributes = array();
    }
    //--------------------------------------------------------------------------


    public static function reset() 
    {
        self::$id = null;
        self::$attributes = array();
    }
    //--------------------------------------------------------------------------
    
    
    public static function addAttributes($newAttributes) 
    {
        if (is_array($newAttributes) && !empty($newAttributes)) {
            self::$attributes = array_merge(self::$attributes, $newAttributes);
        }
    }
    //--------------------------------------------------------------------------


    public static function save()
    {
        self::$attributes['create_time'] = date('Y-m-d H:i:s');
        if (is_null(self::$id)) {
            return false;
        } else {
            return self::updateById(self::$id, self::$attributes);
        }
    }
    //--------------------------------------------------------------------------

    public static function updateById ($id, $attributes) 
    {
        if (empty($id) || !is_array($attributes)) {
            return false;
        }
        
        foreach ($attributes as $key => $value) {
            if (!in_array($key, self::$tableFields) || $key === 'id') {
                unset ($attributes[$key]);
            }
        }
        
        if (empty($attributes)) {
            return false;
        }
        
        $sql  = "UPDATE `". self::tableName ."` SET";
        foreach ($attributes as $key => $value) {
            if (empty($value)) {
                $sql .= " `$key` = null,";
            } else {
                $sql .= " `$key` = '". mysql_real_escape_string($value) ."',";
            } 
        }
        $sql = rtrim($sql, ',');
        
        $sql .= " WHERE `id` = '" . mysql_real_escape_string($id) . "';";
        
        $db = new Database;   
        return $db->query($sql);     
    }  
    //--------------------------------------------------------------------------
    
    public static function getById ($id) 
    {   
        
        $sql  = "SELECT * FROM `". self::tableName ."` WHERE `id` = '" . mysql_real_escape_string($id) . "';";
        
        $db = new Database;   
        return $db->getArray($sql);     
    }  
    //--------------------------------------------------------------------------
    
    public static function getId () 
    {     
        return self::$id;   
    }  
    //--------------------------------------------------------------------------
    
    public static function getLeadsToLog ($leads, $limit = self::leadLogLimit) 
    {     
        $result = array();
        $count = 0;
        foreach ($leads as $lead) {
            $count ++;
            if ($count > $limit) {
                break;
            }
            if (isset($lead['email'])) {
                $result[] = array('email' => $lead['email']);
            }
        }
        return $result;
    }  
    //--------------------------------------------------------------------------
    
    public static function getAll($start = null, $end = null, $keyword = null, $page = '1', $sortBy = null, $sortOrder = null) 
    {
            
        $sql = "SELECT * FROM `". self::tableName ."`";
        
        $wheres = array();
        if (!empty($start)) {
            $start = mysql_real_escape_string($start . ' 00:00:00');
            $wheres[] = " `create_time` >= '$start' ";
        }
        
        if (!empty($end)) {
            $end = mysql_real_escape_string($end . ' 23:59:59');
            $wheres[] = " `create_time` <= '$end' ";
        }
        
        if (!empty($keyword)) {
            $wheres[] = " `leads` like '%$keyword%' ";
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
    // -------------------------------------------------------------------------
    
    public static function countAll($start = null, $end = null, $keyword = null) 
    {
            
        $sql = "SELECT count(*) as count FROM `". self::tableName ."`";    
        
        $wheres = array();
        if (!empty($start)) {
            $start = mysql_real_escape_string($start . ' 00:00:00');
            $wheres[] = " `create_time` >= '$start' ";
        }
        
        if (!empty($end)) {
            $end = mysql_real_escape_string($end . ' 23:59:59');
            $wheres[] = " `create_time` <= '$end' ";
        }
        
        if (!empty($keyword)) {
            $wheres[] = " `leads` like '%$keyword%' ";
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
    
    public static function formatLog($logs) 
    {
        foreach ($logs as $index => $log) {
            $logs[$index]['eligible_campaign_ids'] = unserialize($log['eligible_campaign_ids']);
            $logs[$index]['chosen_campaign_attribute'] = unserialize($log['chosen_campaign_attribute']);
            $logs[$index]['leads'] = unserialize($log['leads']);
        }
        return $logs;
    }
}