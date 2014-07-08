<?php

require_once '../email.php';

class SetupTestData {

    public static function addQueueBuildData($data) 
    {
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
    }
    //--------------------------------------------------------------------------
    
    
    public static function addThrottleData($data) 
    {
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
    }
    //--------------------------------------------------------------------------
}