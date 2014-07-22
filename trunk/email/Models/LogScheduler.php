<?php

class LogScheduler extends Database
{

    // id of processing record
    protected static $id = null;
    
    // array (table_column => column_value)
    protected static $attributes = array();

    const tableName = 'log_scheduler';
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
            
    //--------------------------------------------------------------------------
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

    public static function updateById ($id, $attributes) {
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
                $sql .= " `$key` = '". mysql_escape_string($value) ."',";
            } 
        }
        $sql = rtrim($sql, ',');
        
        $sql .= " WHERE `id` = '" . mysql_escape_string($id) . "';";
        
        $db = new Database;   
        return $db->query($sql);     
    }  
    //--------------------------------------------------------------------------
    
    public static function getById ($id) {   
        
        $sql  = "SELECT * FROM `". self::tableName ."` WHERE `id` = '" . mysql_escape_string($id) . "';";
        
        $db = new Database;   
        return $db->getArray($sql);     
    }  
    //--------------------------------------------------------------------------
    
    public static function getId () {     
        return self::$id;   
    }  
}