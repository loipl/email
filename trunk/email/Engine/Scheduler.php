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

        LogScheduler::init();
        LogScheduler::addAttributes(array('scheduler_name' => $this->cronIdentifier));

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

        $batches = new Engine_Scheduler_Batches($this->leads);
        
        Engine_Scheduler_Channels::pushChannelsToBuildQueue($this->leads);

        $this->removeBlankRecordsFromBuildQueue($this->leads);
        $this->moveRecordsFromBuildQueueToSendQueue($this->leads);
        
        LogScheduler::save();
        LogScheduler::reset();
    }
    //--------------------------------------------------------------------------


    private function exitProcess($message)
    {
        
        LogScheduler::addAttributes(array('message' => $message));
        LogScheduler::save();
        LogScheduler::reset();
        
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
        
        LogScheduler::addAttributes(array(
            'chosen_campaign_id' => $this->campaignId,
            'chosen_campaign_attribute' => $this->campaign->getAttributes()
        ));

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
        
        LogScheduler::addAttributes(array(
            'lead_count' => count($this->leads),
            'leads'      => serialize($this->leads)
        ));

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


    public static function moveRecordsFromBuildQueueToSendQueue($leads)
    {
        if (empty($leads) || !is_array($leads)) {
            return false;
        }
        
        $sendQueueCount = 0;

        foreach($leads AS $lead) {
            $record = new Queue_Build($lead['build_queue_id']);
            
            $delayInfo = Engine_Scheduler::getDelayInfo($record->getEmail(), $record->getChannel());
            
            // if delay time > threshold, ignore the lead, will be removed belows
            if ($delayInfo !== false) {
                
                // if the lead have assigned creative, and delay_time < threshold, send it to queue_send
                if ($record->getHtmlBody() != '' || $record->getTextBody() != '') {
                    
                    if (!Queue_Send::checkQueueSendExist($record->getEmail(), $record->getCreativeId())) {
                        
                        $delayUntil = $delayInfo['delay_until'];
                        $delaySeconds = $delayInfo['delay_seconds'];

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
                            $delayUntil,
                            $delaySeconds
                        );
                    }
                    $sendQueueCount ++;
                }
            }

            $record->removeRecord();
        }
        
        LogScheduler::addAttributes(array('queued_lead_count' => $sendQueueCount));

        return true;
    }
    //--------------------------------------------------------------------------
    
    
    public static function getDelayInfo($email, $channel) {
        $emailDomain = explode('@', $email);

        if (isset($emailDomain[1])) {
            $domain = $emailDomain[1];
        }
        
        if (!empty($domain)) {
            $throttlesByDomain = Throttle::getThrottlesByDomain($domain, $channel);
            $stackingDelayLeads = Queue_Send::getStackingDelayByTLD($domain);
            
            $tldGroup = TldList::getTldGroupByDomain($domain);

            if (!empty($tldGroup)) {
                $throttlesByTldGroup = Throttle::getThrottlesByTldGroup($tldGroup, $channel);
            }
        }
        
        $lead = new Lead($email);
        $sourceCampaign = $lead->getCampaign();
        
        if (!empty($sourceCampaign)) {
            $throttlesBySourceCampaign = Throttle::getThrottlesBySourceCampaign($sourceCampaign, $channel);
        }
        
        $delaySecond = 0;
        
        // get delay seconds by domain throttles
        if (!empty($throttlesByDomain)) {
            self::addDelaySecondByThrottles($throttlesByDomain, $delaySecond);
        }
        
        // get delay seconds by domain throttles
        if (!empty($throttlesBySourceCampaign)) {
            self::addDelaySecondByThrottles($throttlesBySourceCampaign, $delaySecond);
        }

        // get delay seconds by stacking delays
        if (!empty($stackingDelayLeads)) {
            foreach ($stackingDelayLeads as $record) {
                $delaySecond += (int) $record['delay_seconds'];
            }
        }
        
        // get delay seconds by tld group throttles
        if (!empty($throttlesByTldGroup)) {
            self::addDelaySecondByTldGroupThrottles($throttlesByTldGroup, $delaySecond);
        }
        
        if ($delaySecond !== 0) {
            if ($delaySecond > Config::THRESHOLD_DELAY_SECONDS) {
                // abandom the lead
                return false;
            } else {
                return array(
                    'delay_seconds' => $delaySecond,
                    'delay_until' => date('Y-m-d H:i:s', (time() + $delaySecond))
                );
            }
        }
        
        return array(
            'delay_until' => null,
            'delay_seconds' => null
        );
    }
    //--------------------------------------------------------------------------
    
    
    public static function addDelaySecondByThrottles($throttles, &$delaySecond)
    {
        foreach ($throttles as $record) {
            $throttleType = (int) $record['type'];

            switch ($throttleType) {
                case Config::TRANSACTION_TYPE_COMPLAINT:
                    $delaySecond += Config::COMPLAINT_DELAY_SECONDS;
                    break;

                case Config::TRANSACTION_TYPE_HARDBOUNCE:
                    $delaySecond += Config::HARD_BOUNCE_DELAY_SECONDS;
                    break;

                case Config::TRANSACTION_TYPE_SOFTBOUNCE:
                    $delaySecond += Config::SOFT_BOUNCE_DELAY_SECONDS;
                    break;

                default:
                    break;
            }
        }
        
        return true;
    }
    //--------------------------------------------------------------------------
    
    
    public static function addDelaySecondByTldGroupThrottles($throttlesByTldGroup, &$delaySecond)
    {
        foreach ($throttlesByTldGroup as $record) {
            $tldGroup = $record['tld_group'];
            
            switch ($tldGroup) {
                case Config::AOL_TLD_LIST:
                    $delaySecond += Config::AOL_TLD_LIST_DELAY_SECONDS;
                    break;
                
                case Config::MICROSOFT_TLD_LIST:
                    $delaySecond += Config::MICROSOFT_TLD_LIST_DELAY_SECONDS;
                    break;
                
                case Config::GMAIL_TLD_LIST:
                    $delaySecond += Config::GMAIL_TLD_LIST_DELAY_SECONDS;
                    break;
                
                case Config::UNITED_ONLINE_TLD_LIST:
                    $delaySecond += Config::UNITED_ONLINE_TLD_LIST_DELAY_SECONDS;
                    break;
                
                case Config::CABLE_TLD_LIST:
                    $delaySecond += Config::CABLE_TLD_LIST_DELAY_SECONDS;
                    break;
                
                case Config::YAHOO_TLD_LIST:
                    $delaySecond += Config::YAHOO_TLD_LIST_DELAY_SECONDS;
                    break;
                
                default:
                    break;
            }
        }
        
        return true;
    }
    //--------------------------------------------------------------------------
}