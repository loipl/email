<?php

class Engine_Scheduler
{

    protected $leads;
    protected $campaign;
    protected $campaignId;
    protected $attributes;
    protected $creativeIds;
    protected $cronIdentifier;
    protected $suppressionList;

    public function __construct($email = NULL, $campaignId = NULL, $cronIdentifier = NULL)
    {
        if (!empty($cronIdentifier)) {
            $this->cronIdentifier = $cronIdentifier;
        } else {
            $this->cronIdentifier = 'process-scheduler';
        }

        if (empty($email) && empty($campaignId)) {
            $this->setupCampaign();
            $this->setupLeads();
        } else {
            $this->setupCampaign($campaignId);
            $this->setupLeads($email);
        }

        $this->setupCreatives();
        $this->assignCreativesToLeads($this->leads, $this->creativeIds);

        $this->pushActivityAndAssignSubIds($this->leads, $this->campaignId);
        $this->pushSubIdsToBuildQueue($this->leads);

        Engine_Scheduler_Creatives::pushCreativeAndCampaignIdsToBuildQueue($this->leads, $this->campaignId);
        Engine_Scheduler_Channels::pushChannelsToBuildQueue($this->leads);

        $batches = new Engine_Scheduler_Batches($this->leads);

        $this->removeBlankRecordsFromBuildQueue($this->leads);
        $this->moveRecordsFromBuildQueueToSendQueue($this->leads);
    }
    //--------------------------------------------------------------------------


    private function exitProcess($message)
    {
        if (!empty($message)) {
            Locks_Cron::removeLock($this->cronIdentifier);

            die($message);
        }

        Locks_Cron::removeLock($this->cronIdentifier);

        die();
    }
    //--------------------------------------------------------------------------


    private function setupCampaign($campaignId = NULL)
    {
        if (empty($campaignId)) {
            $this->campaignId = Engine_Scheduler_Leads::getRandomCampaignToProcess();
        } else {
            $this->campaignId = $campaignId;
        }

        if (empty($this->campaignId)) {
            self::exitProcess('No Campaigns to Process');
        }

        $this->campaign   = new Campaign($this->campaignId);
        $this->attributes = unserialize($this->campaign->getAttributes());
        $this->suppressionList = $this->campaign->getSuppressionList();

        if (Config::$debugLevel > 0) {
            Logging::logDebugging('[Scheduler: setupCampaign] attributes', serialize($this->attributes));
        }

        return true;
    }
    //--------------------------------------------------------------------------


    private function setupLeads($email = NULL)
    {
        if (empty($email)) {
            if (isset($this->suppressionList) && !empty($this->suppressionList)) {
                $this->leads = Engine_Scheduler_Leads::getLeadsFromCampaignAttributes($this->attributes, $this->suppressionList);
            } else {
                $this->leads = Engine_Scheduler_Leads::getLeadsFromCampaignAttributes($this->attributes);
            }
        } else {
            $this->leads[] = array('email'  => $email);
        }

        if (empty($this->leads)) {
            Probability::addRecord('1', $this->campaignId, '2', '100', NULL, NULL, 1800);

            self::exitProcess('No Leads to Process');
        }

        Engine_Scheduler_Leads::lockLeads($this->leads);
        Engine_Scheduler_Leads::pushLeadsToBuildQueue($this->leads);

        return true;
    }
    //--------------------------------------------------------------------------


    private function setupCreatives()
    {
        $this->creativeIds = unserialize($this->campaign->getCreativeIds());

        if (Config::$debugLevel > 0) {
            Logging::logDebugging('[Scheduler: setupCreatives] creativeIds', serialize($this->creativeIds));
        }

        return true;
    }
    //--------------------------------------------------------------------------


    private function assignCreativesToLeads(&$leads, $creativeIds)
    {
        foreach ($leads AS &$lead) {
            $lead['creative_id'] = Random::getRandomCreativeId($creativeIds);
        }

        if (Config::$debugLevel > 0) {
            Logging::logDebugging('[Scheduler: assignCreativesToLeads] leads', serialize($this->leads));
        }

        return true;
    }
    //--------------------------------------------------------------------------


    private function pushActivityAndAssignSubIds(&$leads, $campaignId)
    {
        if (empty($leads) || empty($campaignId)) {
            return false;
        }

        foreach($leads AS &$lead) {
            $lead['sub_id'] = Activity::addActivity($lead['email'], $campaignId);
        }

        return true;
    }
    //--------------------------------------------------------------------------


    private function pushSubIdsToBuildQueue($leads)
    {
        if (empty($leads) || !is_array($leads)) {
            return false;
        }

        foreach($leads AS $lead) {
            Queue_Build::addSubId($lead['build_queue_id'], $lead['sub_id']);
        }

        return true;
    }
    //--------------------------------------------------------------------------


    private function removeBlankRecordsFromBuildQueue($leads)
    {
        $blankRecords = Queue_Build::getBlankRecords($leads);

        $blankCount = 0;

        if (!empty($blankRecords)) {
            foreach($blankRecords AS $record) {
                Queue_Build::removeRecordById($record['id']);
                Activity::removeActivity($record['sub_id']);
                Lead::removeLock($record['email']);

                if (Config::$debugLevel > 0) {
                    Logging::logDebugging('[Batches: removeBlankRecordsFromBuildQueue] removed lead', serialize($record));
                }

                $blankCount++;
            }
        }

        if (count($leads) == $blankCount) {
            Probability::addRecord('1', $this->campaignId, '2', '100', NULL, NULL, 1800);
        }

        return true;
    }
    //--------------------------------------------------------------------------


    private function moveRecordsFromBuildQueueToSendQueue($leads)
    {
        if (empty($leads) || !is_array($leads)) {
            return false;
        }

        foreach($leads AS $lead) {
            $record = new Queue_Build($lead['build_queue_id']);
            
            $delayUntil = $this->getThrottleDelayUntil($record->getEmail(), $record->getChannel(), $record->getCreativeId(), $record->getCampaignId(), $record->getCategoryId());
            
            // if delay time > threshold, ignore the lead, will be removed belows
            if ($delayUntil !== false) {
                
                // if the lead have assigned creative, and delay_time < threshold, send it to queue_send
                if ($record->getHtmlBody() != '' || $record->getTextBody() != '') {
                    Queue_Send::addRecord(
                        $record->getEmail(),
                        $record->getFrom(),
                        $record->getCampaignId(),
                        $record->getCreativeId(),
                        $record->getCategoryId(),
                        $record->getSenderEmail(),
                        $record->getSubject(),
                        $record->getHtmlBody(),
                        $record->getTextBody(),
                        $record->getSubId(),
                        $record->getChannel(),
                        $delayUntil
                    );
                }
            }

            $record->removeRecord();
        }

        return true;
    }
    //--------------------------------------------------------------------------
    
    
    private function getThrottleDelayUntil($email, $channelId, $creativeId, $campaignId, $categoryId) {
        $emailDomain = explode('@', $email);
        $domain = $emailDomain[1];
        
        $throttleType = Throttle::getThrottleExistsType($domain, $channelId, $creativeId, $campaignId, $categoryId);
        
        if ($throttleType) {
            $delaySecond = 0;
            
            switch ($throttleType) {
                case Config::TRANSACTION_TYPE_COMPLAINT:
                    $delaySecond = Config::COMPLAINT_DELAY_SECONDS;
                    break;
                
                case Config::TRANSACTION_TYPE_HARDBOUNCE:
                    $delaySecond = Config::HARD_BOUNCE_DELAY_SECONDS;
                    break;
                
                case Config::TRANSACTION_TYPE_SOFTBOUNCE:
                    $delaySecond = Config::SOFT_BOUNCE_DELAY_SECONDS;
                    break;
                
                default:
                    break;
            }

            if ($delaySecond > Config::THRESHOLD_DELAY_SECONDS) {
                // abandom the lead
                return false;
            } else {
                return date('Y-m-d H:i:s', (time() + $delaySecond));
            }
        }
        
        return NULL;
    }
    //--------------------------------------------------------------------------
}