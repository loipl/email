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
    protected $stackingDelay;
    protected $throttles;
    
    public function __construct($email = NULL, $campaignId = NULL, $cronIdentifier = NULL)
    {
        if (!empty($cronIdentifier)) {
            $this->cronIdentifier = $cronIdentifier;
        } else {
            $this->cronIdentifier = 'process-scheduler';
        }

        LogScheduler::init();
        LogScheduler::addAttributes(array('scheduler_name' => $this->cronIdentifier));
        
        $this->stackingDelay = Queue_Send::getExistStackingDelayByTLD();
        $this->throttles = Throttle::getAllThrottles();
        
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
        $this->moveRecordsFromBuildQueueToSendQueue($this->leads, $this->stackingDelay, $this->throttles);
        
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
        
        $this->removeExceedingThrottleThresholdLeads($this->leads);
        
        if (empty($this->leads)) {
            Probability::addRecord('1', $this->campaignId, '2', '100', NULL, NULL, 1800);

            self::exitProcess('No Leads to Process');
        }
        
        LogScheduler::addAttributes(array(
            'lead_count' => count($this->leads),
            'leads'      => serialize(LogScheduler::getLeadsToLog($this->leads))
        ));

        Engine_Scheduler_Leads::lockLeads($this->leads);
        Engine_Scheduler_Leads::pushLeadsToBuildQueue($this->leads);

        return true;
    }
    //--------------------------------------------------------------------------
    
    
    private function removeExceedingThrottleThresholdLeads(&$leads)
    {
        $stackingDelay = $this->stackingDelay;

        if (!empty($leads)) {
            foreach ($leads as $id => $lead) {
                $emailDomain = explode('@', $lead['email']);

                if (isset($emailDomain[1])) {
                    $domain = $emailDomain[1];
                }
                
                // sanity check
                if (empty($lead['email']) || empty($domain)) {
                    unset($leads[$id]);
                    continue;
                }
                
                if (!empty($domain)) {
                    $delaySeconds = $this->getDelaySeconds($lead['email'], $domain, $this->throttles);
                    
                    // add stacking delay if exist
                    if (!empty($delaySeconds) && !empty($stackingDelay[$domain])) {
                        $delaySeconds += $stackingDelay[$domain];
                    }
                    
                    // unset lead that exceed threshold
                    if (!empty($delaySeconds) && $delaySeconds >= Config::THRESHOLD_DELAY_SECONDS) {
                        unset($leads[$id]);
                        continue;
                    }
                    
                    // add stacking delay data
                    if (!empty($delaySeconds)) {
                        if (isset($stackingDelay[$domain])) {
                            if ($delaySeconds > $stackingDelay[$domain]) {
                                $stackingDelay[$domain] = $delaySeconds;
                            }
                        } else {
                            $stackingDelay[$domain] = $delaySeconds;
                        }
                    }
                }
            }
        }
        
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


    public static function moveRecordsFromBuildQueueToSendQueue($leads, $stackingDelay, $throttles)
    {
        if (empty($leads) || !is_array($leads)) {
            return false;
        }
        
        $sendQueueCount = 0;

        foreach($leads AS $lead) {
            $record = new Queue_Build($lead['build_queue_id']);
            $email = $record->getEmail();
            
            $emailDomain = explode('@', $email);

            if (isset($emailDomain[1])) {
                $domain = $emailDomain[1];
            }
            
            // sanity check
            if (empty($email) || empty($domain)) {
                $record->removeRecord();
                continue;
            }
            
            $delaySeconds = Engine_Scheduler::getDelaySeconds($email, $domain, $throttles, $record->getChannel());

            // add stacking delay if exist
            if (!empty($delaySeconds) && !empty($stackingDelay[$domain])) {
                $delaySeconds += $stackingDelay[$domain];
            }
            
            // calculate delay until
            $delayUntil = null;
            if (!empty($delaySeconds)) {
                $delayUntil = date('Y-m-d H:i:s', (time() + $delaySeconds));
            }
            
            // add stacking delay data
            if (!empty($delaySeconds)) {
                if (isset($stackingDelay[$domain])) {
                    if ($delaySeconds > $stackingDelay[$domain]) {
                        $stackingDelay[$domain] = $delaySeconds;
                    }
                } else {
                    $stackingDelay[$domain] = $delaySeconds;
                }
            }
            
            if ($delaySeconds >= Config::THRESHOLD_DELAY_SECONDS) {
                Logging::logDebugging('FAILED in later check', $email . ' - ' . $delaySeconds);
            }
            
            // if the lead have assigned creative, send it to queue_send
            if ($record->getHtmlBody() != '' || $record->getTextBody() != '') {

                if (!Queue_Send::checkQueueSendExist($email, $record->getCreativeId())) {

                    Queue_Send::addRecord(
                        $email,
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

            $record->removeRecord();
        }
        
        LogScheduler::addAttributes(array('queued_lead_count' => $sendQueueCount));

        return true;
    }
    //--------------------------------------------------------------------------
    
    
    public static function getDelaySeconds($email, $domain, $throttles, $channel = null) {
        
        if (!empty($domain)) {
            $throttlesByDomain = Throttle::getThrottlesByDomain($throttles, $domain, $channel);
            
            $tldGroup = TldList::getTldGroupByDomain($domain);

            if (!empty($tldGroup)) {
                $throttlesByTldGroup = Throttle::getThrottlesByTldGroup($throttles, $tldGroup, $channel);
            }
        }
        
        $lead = new Lead($email);
        $sourceCampaign = $lead->getCampaign();
        
        if (!empty($sourceCampaign)) {
            $throttlesBySourceCampaign = Throttle::getThrottlesBySourceCampaign($throttles, $sourceCampaign, $channel);
        }
        
        $delaySeconds = 0;
        
        // get delay seconds by domain throttles
        if (!empty($throttlesByDomain)) {
            self::addDelaySecondByThrottles($throttlesByDomain, $delaySeconds);
        }
        
        // get delay seconds by source campaign
        if (!empty($throttlesBySourceCampaign)) {
            self::addDelaySecondByThrottles($throttlesBySourceCampaign, $delaySeconds);
        }

        // get delay seconds by tld group throttles
        if (!empty($throttlesByTldGroup)) {
            self::addDelaySecondByThrottles($throttlesByTldGroup, $delaySeconds);
        }
        
        return $delaySeconds;
    }
    //--------------------------------------------------------------------------
    
    
    public static function addDelaySecondByThrottles($matchedThrottles, &$delaySecond)
    {
        foreach ($matchedThrottles as $type) {
            $throttleType = (int) $type;

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
}