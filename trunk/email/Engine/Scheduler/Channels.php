<?php

class Engine_Scheduler_Channels
{

    public static function pushChannelsToBuildQueue($leads)
    {
        if (empty($leads) || !is_array($leads)) {
            return false;
        }

        $channelId = self::getChannelId();

        foreach ($leads AS $lead) {
            Queue_Build::addChannelData($lead['build_queue_id'], $channelId);
        }

        return true;
    }
    //--------------------------------------------------------------------------


    private static function getChannelId()
    {
        return '1';
    }
}