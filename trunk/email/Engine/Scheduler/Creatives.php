<?php

class Engine_Scheduler_Creatives
{

    public static function getCreativeData($creativeId)
    {
        if (empty($creativeId) || !is_numeric($creativeId)) {
            return false;
        }

        $creative = new Creative($creativeId);

        $creativeData['category_id'] = $creative->getCategoryId();
        $creativeData['senderId']   = $creative->getSenderId();
        $creativeData['from_name']  = $creative->getFrom();
        $creativeData['subject']    = $creative->getSubject();
        $creativeData['html_body']  = $creative->getHtmlBody();
        $creativeData['text_body']  = $creative->getTextBody();

        $sender = new Sender($creativeData['senderId']);
        $senderEmail = $sender->getName() . '@' . $sender->getDomain();

        $creativeData['sender_email'] = $senderEmail;

        return $creativeData;
    }
    //--------------------------------------------------------------------------


    public static function pushCreativeAndCampaignIdsToBuildQueue($creativesByLead, $campaignId)
    {
        if (!is_array($creativesByLead)) {
            return false;
        }

        foreach ($creativesByLead AS $lead) {
            Queue_Build::addCreativeAndCampaignIds($lead['build_queue_id'], $lead['creative_id'], $campaignId);
        }

        return true;
    }
    //--------------------------------------------------------------------------


    public static function addHtmlFooter($email, $subId, $senderDomain, $clickSubdomain, &$htmlBody, $footer)
    {
        HTML::addHtmlFooter($email, $subId, $senderDomain, $clickSubdomain, $htmlBody, $footer);

        return true;
    }
    //--------------------------------------------------------------------------


    public static function addHtmlPixel($email, $subId, $senderDomain, &$htmlBody)
    {
        HTML::addHtmlPixel($email, $subId, $senderDomain, $htmlBody);

        return true;
    }
    //--------------------------------------------------------------------------


    public static function addTextFooter($email, $subId, $senderDomain, $clickSubdomain, &$textBody, $footer)
    {
        HTML::addTextFooter($email, $subId, $senderDomain, $clickSubdomain, $textBody, $footer);

        return true;
    }
    //--------------------------------------------------------------------------


    public static function getUnsubUrl($clickSubdomain, $senderDomain, $email, $subId)
    {
        $unsubUrl = HTML::getUnsub($clickSubdomain, $senderDomain, $email, $subId);

        return $unsubUrl;
    }
    //--------------------------------------------------------------------------
}