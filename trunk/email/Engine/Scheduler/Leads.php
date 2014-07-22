<?php

class Engine_Scheduler_Leads
{

    public static function getRandomCampaignToProcess()
    {
        $campaigns = self::getEligibleCampaigns();

        if (empty($campaigns)) {
            return false;
        } else {
            $campaign = self::selectRandomCampaign($campaigns);
            LogScheduler::addAttributes(array(
                'eligible_campaign_count' => count($campaigns),
                'eligible_campaign_ids'   => serialize($campaigns) 
            ));
        }

        if (!empty($campaign) && is_numeric($campaign)) {
            if (Config::$debugLevel > 0) {
                Logging::logDebugging('[Scheduler: setupCampaign] getRandomCampaignToProcess', $campaign);
            }
            return $campaign;
        }

        if (Config::$debugLevel > 0) {
            Logging::logDebugging('[Scheduler: setupCampaign] getRandomCampaignToProcess', 'Selection Failed (returned false)');
        }

        return false;
    }
    //--------------------------------------------------------------------------


    public static function getLeadsFromCampaignAttributes($attributes, $suppressionList = null)
    {
        $lead = new Lead();
        $leads = $lead->getLeads(new Database, $attributes);

        if (!empty($leads)) {
            if (Config::$debugLevel > 0) {
                Logging::logDebugging('[Scheduler: setupLeads] getLeadsFromCampaignAttributes', serialize($leads));
            }

            return $leads;
        }

        if (Config::$debugLevel > 0) {
            Logging::logDebugging('[Scheduler: setupLeads] getLeadsFromCampaignAttributes', 'Leads Empty for ' . serialize($attributes));
        }

        return false;
    }
    //--------------------------------------------------------------------------


    public static function lockLeads($leads)
    {
        if (!is_array($leads)) {
            return false;
        }

        foreach ($leads AS $lead) {
            Lead::setLock($lead['email'], 'xxx');
        }

        return true;
    }
    //--------------------------------------------------------------------------


    public static function pushLeadsToBuildQueue(&$leads)
    {
        if (!is_array($leads)) {
            return false;
        }

        foreach ($leads AS &$lead) {
            Queue_Build::addRecord($lead['email']);
            $lead['build_queue_id'] = mysql_insert_id();
        }

        return true;
    }
    //--------------------------------------------------------------------------


    private static function selectRandomCampaign($campaigns)
    {
        if (is_array($campaigns)) {
            return $campaigns[array_rand($campaigns)];
        } else {
            return false;
        }
    }
    //--------------------------------------------------------------------------


    private static function getEligibleCampaigns()
    {
        $eligibleArray = Campaign::getEligibleCampaigns();
        $suppressedCampaigns = Probability::getSuppressedCampaigns();

        if (empty($eligibleArray)) {
            Logging::logDebugging('[Scheduler: setupCampaign] No eligible Campaigns', null);
            return false;
        }

        foreach ($eligibleArray AS $key => $value) {
            $eligibleCampaigns[] = $value['id'];
        }

        if (!empty($suppressedCampaigns)) {
            foreach ($suppressedCampaigns AS $campaign) {
                if (($key = array_search($campaign['metric_id'], $eligibleCampaigns)) !== false) {
                    unset($eligibleCampaigns[$key]);
                }
            }
        }

        if (Config::$debugLevel > 0) {
            Logging::logDebugging('[Scheduler: setupCampaign] getEligibleCampaigns', serialize($eligibleCampaigns));
        }

        if (!empty($eligibleCampaigns) && is_array($eligibleCampaigns)) {
            foreach ($eligibleCampaigns AS $row) {
                $eligibleList[] = $row;
            }

            if (!empty($eligibleList)) {
                return $eligibleList;
            }
        } else {
            if (Config::$debugLevel > 0) {
                Logging::logDebugging('[Scheduler: setupCampaign] getEligibleCampaigns', 'No campaigns returned');
            }

            return false;
        }

        return false;
    }
    //--------------------------------------------------------------------------
}