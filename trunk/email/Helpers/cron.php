<?php

class Cron
{

    public static function checkConcurrency($identifier)
    {
        if (Locks_Cron::isLocked($identifier)) {
            die();
        } else {
            Locks_Cron::setLock($identifier);
        }
    }
    //--------------------------------------------------------------------------
}