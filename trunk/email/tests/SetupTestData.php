<?php

require_once '../email.php';

class SetupTestData {
    
    public static function resetTableData($tableName) {
        $db = new Database();
        
        if (strtolower($db->getDatabaseName()) === strtolower(Config::$testDatabase['database'])) {
            $sql = 'TRUNCATE '. $tableName;
            
            $db->query($sql);

            return true;
        } else {
            echo '=== TEST DATABASE NOT MATCH ===';
            die;
        }
    }
    //--------------------------------------------------------------------------
    
    
    public static function addLeadData($data)
    {
        $db = new Database();
        
        if (strtolower($db->getDatabaseName()) === strtolower(Config::$testDatabase['database'])) {
            if ( ! empty($data)) {
                foreach ($data as $row) {
                    try {
                        Lead::addRecord($row);
                    } catch (Exception $e) {
                        echo '*** ERROR: Cannot add new lead at email: '. $row['email'];
                        continue;
                    }
                }
            }
        } else {
            echo '=== TEST DATABASE NOT MATCH ===';
            die;
        }
    }
    //--------------------------------------------------------------------------
    
    
    public static function addQueueBuildData($data) 
    {
        $db = new Database();
        
        if (strtolower($db->getDatabaseName()) === strtolower(Config::$testDatabase['database'])) {
            if ( ! empty($data)) {
                foreach ($data as $row) {
                    try {
                        Queue_Build::addFullRecord($row);
                    } catch (Exception $e) {
                        echo '*** ERROR: Cannot add new queue_build at email: '. $row['email'];
                        continue;
                    }
                }
            }
        } else {
            echo '=== TEST DATABASE NOT MATCH ===';
            die;
        }
    }
    //--------------------------------------------------------------------------
    
    
    public static function addQueueSendData($data) 
    {
        $db = new Database();
        
        if (strtolower($db->getDatabaseName()) === strtolower(Config::$testDatabase['database'])) {
            if ( ! empty($data)) {
                foreach ($data as $row) {
                    try {
                        Queue_Send::addRecord($row['email'], $row['from_name'], $row['campaign_id'], $row['creative_id'], $row['category_id'], $row['sender_email'], 
                                $row['subject'], $row['html_body'], $row['text_body'], $row['sub_id'], $row['channel'], $row['delay_until'], $row['delay_seconds']);
                    } catch (Exception $e) {
                        echo '*** ERROR: Cannot add new queue_send at email: '. $row['email'];
                        continue;
                    }
                }
            }
        } else {
            echo '=== TEST DATABASE NOT MATCH ===';
            die;
        }
    }
    //--------------------------------------------------------------------------
    
    
    public static function addThrottleData($data) 
    {
        $db = new Database();
        
        if (strtolower($db->getDatabaseName()) === strtolower(Config::$testDatabase['database'])) {
            if ( ! empty($data)) {
                foreach ($data as $row) {
                    try {
                        Throttle::addThrottle($row);
                    } catch (Exception $e) {
                        echo '*** ERROR: Cannot add new throtte at domain: '. $row['domain'];
                        continue;
                    }
                }
            }
        } else {
            echo '=== TEST DATABASE NOT MATCH ===';
            die;
        }
    }
    //--------------------------------------------------------------------------
    
     public static function resetLogScheduler() {
        $db = new Database();
        $tableName = 'log_scheduler';
        
        if (strtolower($db->getDatabaseName()) === strtolower(Config::$testDatabase['database'])) {
            $sql = 'TRUNCATE '. $tableName;
            
            $db->query($sql);

            return true;
        } else {
            echo '=== TEST DATABASE NOT MATCH ===';
            die;
        }
    }
}