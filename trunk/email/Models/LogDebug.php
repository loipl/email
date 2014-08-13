<?php

class LogDebug extends Database
{

    // id of processing record
    protected static $id = null;
    
    // array (table_column => column_value)
    protected static $attributes = array();
    
    const tableName = 'error_log_debug';
    const pageSize = 20;
    const leadLogLimit = 1000;
    protected static $tableFields = array (
                                        'id', 
                                        'date_time', 
                                        'description', 
                                        'data'
                                    );

    
    //--------------------------------------------------------------------------
    
    public static function getAll($start = null, $end = null, $keyword = null, $page = '1') 
    {
            
        $sql = "SELECT * FROM `". self::tableName ."`";
        
        $wheres = array();
        if (!empty($start)) {
            $start = mysql_real_escape_string($start . ' 00:00:00');
            $wheres[] = " `datetime` >= '$start' ";
        }
        
        if (!empty($end)) {
            $end = mysql_real_escape_string($end . ' 23:59:59');
            $wheres[] = " `datetime` <= '$end' ";
        }
        
        if (!empty($keyword)) {
            $wheres[] = " `description` like '%$keyword%' OR `data` like '%$keyword%' ";
        }
        
        if (!empty($wheres)) {
            $sql .= " WHERE" . implode('AND', $wheres);
        }
        
        $sql .= " ORDER BY `id` DESC";
        
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
            $wheres[] = " `datetime` >= '$start' ";
        }
        
        if (!empty($end)) {
            $end = mysql_real_escape_string($end . ' 23:59:59');
            $wheres[] = " `datetime` <= '$end' ";
        }
        
        if (!empty($keyword)) {
            $wheres[] = " `description` like '%$keyword%' OR `data` like '%$keyword%' ";
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
