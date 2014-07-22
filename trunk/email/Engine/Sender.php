<?php

class Engine_Sender
{

    protected $id;
    protected $created;
    protected $locked;
    protected $email;
    protected $campaignId;
    protected $creativeId;
    protected $categoryId;
    protected $fromName;
    protected $senderEmail;
    protected $subject;
    protected $htmlBody;
    protected $textBody;
    protected $subId;
    protected $channelId;

    public function __construct() {
    }
    //--------------------------------------------------------------------------


    public function processSendQueue($limit)
    {
        if (empty($limit)) {
            $limit = 100;
        }

        $rows = Queue_Send::getUnlockedRowIds($limit);

        if (!empty($rows)) {
            foreach ($rows AS $row) {
                Queue_Send::setLockById($row['id']);
            }

            foreach ($rows AS $row) {
                $sendRecord = new Queue_Send($row['id']);

                $this->id          = $row['id'];
                $this->created     = $sendRecord->getCreated();
                $this->locked      = $sendRecord->getLocked();
                $this->email       = $sendRecord->getEmail();
                $this->campaignId  = $sendRecord->getCampaignId();
                $this->creativeId  = $sendRecord->getCreativeId();
                $this->categoryId  = $sendRecord->getCategoryId();
                $this->fromName    = $sendRecord->getFromName();
                $this->senderEmail = $sendRecord->getSenderEmail();
                $this->subject     = $sendRecord->getSubject();
                $this->htmlBody    = $sendRecord->getHtmlBody();
                $this->textBody    = $sendRecord->getTextBody();
                $this->subId       = $sendRecord->getSubId();
                $this->channelId   = $sendRecord->getChannelId();
                
                if ($this->send($this->email, $this->campaignId, $this->creativeId, $this->categoryId, $this->fromName, $this->senderEmail, $this->subject, $this->htmlBody, $this->textBody, $this->subId, $this->channelId)) {
                    $sendRecord->removeRecord();
                }
            }
        }
    }
    //--------------------------------------------------------------------------

    
    public function sendSingleEmail($sendQueueId = NULL)
    {
        if (empty($sendQueueId)) {
            return false;
        }

        $sendRecord = new Queue_Send($sendQueueId);
        $sendRecord->setLock();

        $this->id          = $sendQueueId;
        $this->created     = $sendRecord->getCreated();
        $this->locked      = $sendRecord->getLocked();
        $this->email       = $sendRecord->getEmail();
        $this->campaignId  = $sendRecord->getCampaignId();
        $this->creativeId  = $sendRecord->getCreativeId();
        $this->categoryId  = $sendRecord->getCategoryId();
        $this->fromName    = $sendRecord->getFromName();
        $this->senderEmail = $sendRecord->getSenderEmail();
        $this->subject     = $sendRecord->getSubject();
        $this->htmlBody    = $sendRecord->getHtmlBody();
        $this->textBody    = $sendRecord->getTextBody();
        $this->subId       = $sendRecord->getSubId();
        $this->channelId   = $sendRecord->getChannelId();

        if ($this->send($this->email, $this->campaignId, $this->creativeId, $this->categoryId, $this->fromName, $this->senderEmail, $this->subject, $this->htmlBody, $this->textBody, $this->subId, $this->channelId)) {
            $sendRecord->removeRecord();
        }

    }
    //--------------------------------------------------------------------------


    private function send($email, $campaignId, $creativeId, $categoryId, $fromName, $senderEmail, $subject, $htmlBody, $textBody, $subId, $channelId)
    {
        $this->pushUpdatesToActivityTable($subId, $channelId, $creativeId, $categoryId, $senderEmail);

        $channel = new Channel($channelId);

        $channelClass = $channel->getClass();

        if (!empty($channelClass)) {
            $channelObject = new $channelClass;

            $senderDomain = explode('@', $senderEmail);
            $unsubUrl = HTML::getUnsub(Config::$subdomains['clicks'], $senderDomain[1], $email, $subId);

            $channelObject->sendEmail($email, $fromName, $senderEmail, $subject, $htmlBody, $textBody, $subId, $unsubUrl, true);

            if ($channelObject->getLastStatus() == 'sent') {
                Lead::scoreSend($email);
                Campaign::addSentCount($campaignId);
            } else {
                if (isset($subId) && is_numeric($subId) && $subId > 0) {
                    Activity::removeActivity($subId);

                    return false;
                }
            }

            Lead::removeLock($email);
        } else {
            return false;
        }

        return true;
    }
    //--------------------------------------------------------------------------


    private function removeActivity($subId)
    {
        if (Activity::removeActivity($subId)) {
            return true;
        }

        return false;
    }
    //--------------------------------------------------------------------------


    private function pushUpdatesToActivityTable($subId, $channelId, $creativeId, $categoryId, $senderEmail)
    {
        if (empty($subId) || empty($channelId)) {
            return false;
        }

        Activity::addSendProcessData($subId, $channelId, $creativeId, $categoryId, $senderEmail);

        return true;
    }
    //--------------------------------------------------------------------------


    private function unlockLead($email)
    {
        if (Lead::removeLock($email)) {
            return true;
        }

        return false;
    }
    //--------------------------------------------------------------------------
}