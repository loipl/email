<?php

class Engine_Scheduler_Batches
{

    protected $buildQueueRows;
    protected $externalCreativeRows;
    protected $localCreativeRows;

    public function __construct($leadData = NULL)
    {
        if (empty($leadData)) {
            // Select batches from DB
        } else {
            foreach ($leadData AS $row) {
                $this->buildQueueRows[$row['build_queue_id']] = $row;
            }
        }

        $this->sortExternalAndLocalCreatives($this->buildQueueRows);
        $this->pushLocalCreativesToBuildQueue($this->localCreativeRows, $this->buildQueueRows);
        $this->pushExternalCreativesToBuildQueue($this->externalCreativeRows, $this->buildQueueRows);

        return true;
    }
    //--------------------------------------------------------------------------


    private function sortExternalAndLocalCreatives($rowData)
    {
        foreach ($rowData AS $row) {
            $className = Creative::getClassById($row['creative_id']);

            if (!empty($className)) {
                $this->externalCreativeRows[$className]['creative_id'] = $row['creative_id'];
                $this->externalCreativeRows[$className]['row_ids'][] = $row['build_queue_id'];
            } else {
                $this->localCreativeRows[$row['creative_id']][] = $row['build_queue_id'];
            }
        }

        return true;
    }
    //--------------------------------------------------------------------------


    private function pushLocalCreativesToBuildQueue($localCreativeRows)
    {
        if (empty($localCreativeRows)) {
            return false;
        }

        foreach ($localCreativeRows AS $creativeId => $buildQueueIds) {
            $creativeData = Engine_Scheduler_Creatives::getCreativeData($creativeId);

            foreach ($buildQueueIds AS $id) {
                $encodedData = $this->encodeData($creativeData, $id);
                Queue_Build::addCreativeData($id, $encodedData);
            }
        }

        return true;
    }
    //--------------------------------------------------------------------------


    private function pushExternalCreativesToBuildQueue($externalCreativeRows, $rowData)
    {
        if (empty($externalCreativeRows)) {
            return false;
        }

        foreach ($externalCreativeRows AS $className => $data) {
            unset($emailArray);
            unset($externalData);
            unset($externalEmails);
            unset($senderId);
            unset($senderEmail);

            $senderId     = Creative::getSenderIdById($externalCreativeRows[$className]['creative_id']);
            $sender       = new Sender($senderId);
            $senderEmail  = $sender->getName() . '@' . $sender->getDomain();

            foreach ($data['row_ids'] AS $id) {
                $emailArray[] = $rowData[$id];
            }

            $builtArray = Builder::buildLeadData($emailArray);

            $externalAdapter = new $className;

            $externalEmails = $externalAdapter->buildPayload($builtArray);
            $externalAdapter->send($externalEmails);
            $externalAdapter->getResult();
            $externalData = $externalAdapter->buildArrayByRecipientKey($builtArray);

            if (!empty($externalData)) {
                foreach($externalData AS $externalRow) {
                    unset($creativeData);

                    $creativeData['category_id'] = $externalRow['categoryId'];
                    $creativeData['senderId'] = $senderId;
                    $creativeData['from_name'] = $externalRow['friendlyFrom'];
                    $creativeData['subject'] = $externalRow['subject'];
                    $creativeData['html_body'] = $externalRow['htmlBody'];
                    $creativeData['text_body'] = $externalRow['textBody'];
                    $creativeData['sender_email'] = $senderEmail;

                    $encodedData = $this->encodeData($creativeData, $externalRow['build_queue_id']);
                    Queue_Build::addCreativeData($externalRow['build_queue_id'], $encodedData);
                }
            }
        }

        return true;
    }
    //--------------------------------------------------------------------------


    private function encodeData($creativeData, $id)
    {
        $encodedData  = $creativeData;
        $rowData      = $this->buildQueueRows[$id];
        $senderDomain = explode('@', $encodedData['sender_email']);

        $footerId     = Sender::getFooterIdById($creativeData['senderId']);
        $footer       = new Footer($footerId);

        HTML::doEncoding($rowData['email'], $rowData['sub_id'], $senderDomain[1], $encodedData['html_body'], $encodedData['text_body']);

        if (!empty($encodedData['html_body'])) {
            HTML::addHtmlFooter($rowData['email'], $rowData['sub_id'], $senderDomain[1], Config::$subdomains['clicks'], $encodedData['html_body'], $footer);
            HTML::addHtmlPixel($rowData['email'], $rowData['sub_id'], $senderDomain[1], $encodedData['html_body']);
        }

        if (!empty($encodedData['text_body'])) {
            HTML::addTextFooter($rowData['email'], $rowData['sub_id'], $senderDomain[1], Config::$subdomains['clicks'], $encodedData['text_body'], $footer);
        }

        return $encodedData;
    }
    //--------------------------------------------------------------------------
}