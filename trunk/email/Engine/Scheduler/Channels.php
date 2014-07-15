<?php

class Engine_Scheduler_Channels
{

    public static function pushChannelsToBuildQueue($leads)
    {
        if (empty($leads) || !is_array($leads)) {
            return false;
        }
        
        $allChannels = Channel::getAllChannels();
        
        foreach ($leads AS $lead) {
            $queueBuild = new Queue_Build($lead['build_queue_id']);
            $creativeId = $queueBuild->getCreativeId();
            $categoryId = $queueBuild->getCategoryId();
            
            $throttledChannelsByCreativeId = Throttle::getThrottledChannelsByCreativeId($creativeId);
            $throttledChannelsByCategoryId = Throttle::getThrottledChannelsByCategoryId($categoryId);
            
            $throttledChannels = array_unique(array_merge($throttledChannelsByCreativeId, $throttledChannelsByCategoryId));
            
            $channelId = self::getChannelId($allChannels, $throttledChannels);
            Queue_Build::addChannelData($lead['build_queue_id'], $channelId);
        }

        return true;
    }
    //--------------------------------------------------------------------------


    private static function getChannelId($allChannels, $throttledChannels)
    {
        $channels = array_values(array_diff($allChannels, $throttledChannels));
        
        if (!empty($channels)) {
            return $channels[mt_rand(0, count($channels) - 1)];
        } else {
            return $allChannels[mt_rand(0, count($allChannels) - 1)];
        }
    }
}