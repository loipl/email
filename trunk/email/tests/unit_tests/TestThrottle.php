<?php

$skipOtherTests = 1;
defined('RUN_ALL_TESTS') or require_once '../tests.php';

class TestThrottle extends UnitTestCase
{
    public function testThrotleHardBounce()
    {
        // reset data
        SetupTestData::resetQueueBuildData();
        SetupTestData::resetQueueSendData();
        SetupTestData::resetThrottleData();
        
        // add test data
        SetupTestData::addQueueBuildData(array(
            array(
                'email'         => 'test@edgeprod.com',
                'stage'         => '1',
                'campaign_id'   => '3',
                'creative_id'   => '106',
                'category_id'   => '7',
                'from_name'     => 'Potato Express',
                'sender_email'  => 'jason@matchquota.com',
                'subject'       => 'Fast and Easy Potatoes',
                'html_body'     => 'html body',
                'text_body'     => 'text body',
                'sub_id'        => '1',
                'channel'       => '1'
            ),
            array(
                'email'         => 'dom@leadwrench.com',
                'stage'         => '1',
                'campaign_id'   => '3',
                'creative_id'   => '106',
                'category_id'   => '7',
                'from_name'     => 'Potato Express',
                'sender_email'  => 'jason@matchquota.com',
                'subject'       => 'Fast and Easy Potatoes',
                'html_body'     => 'html body',
                'text_body'     => 'text body',
                'sub_id'        => '2',
                'channel'       => '1'
            ),
            array(
                'email'         => 'dom@edgeprod.com',
                'stage'         => '1',
                'campaign_id'   => '3',
                'creative_id'   => '107',
                'category_id'   => '8',
                'from_name'     => 'Potato Express BOGO',
                'sender_email'  => 'jason@matchquota.com',
                'subject'       => 'Fast and Easy Potatoes',
                'html_body'     => 'html body',
                'text_body'     => 'text body',
                'sub_id'        => '3',
                'channel'       => '1'
            ),
        ));
        
        SetupTestData::addThrottleData(array(
            array(
                'type'          => '6',
                'domain'        => 'edgeprod.com',
                'channel'       => '1',
                'creative_id'   => '106',
                'campaign_id'   => '3',
                'category_id'   => '7'
            ),
            array(
                'type'          => '4',
                'domain'        => 'leadwrench.com',
                'channel'       => '1',
                'creative_id'   => '100',
                'campaign_id'   => '5',
                'category_id'   => '1'
            )
        ));
        
        // execute function
        $leads = array(
            0 => array('build_queue_id' => '1'),
            1 => array('build_queue_id' => '2'),
            2 => array('build_queue_id' => '3')
        );
        
        $startTime = $startTime = time();
        Engine_Scheduler::moveRecordsFromBuildQueueToSendQueue($leads);
        $send = new Queue_Send(1);
        $delayUntil = $send->getDelayUntil();
        
        // compare results
        if (! is_null($delayUntil) && intval($delayUntil) > 0) {
            $this->assertEqual(Config::HARD_BOUNCE_DELAY_SECONDS, strtotime($delayUntil) - $startTime);
        } else {
            $this->assertEqual(1, 2);
        }
    }
    //--------------------------------------------------------------------------
    
    
    public function testThrotleSoftBounce()
    {
        // reset data
        SetupTestData::resetQueueBuildData();
        SetupTestData::resetQueueSendData();
        SetupTestData::resetThrottleData();
        
        // add test data
        SetupTestData::addQueueBuildData(array(
            array(
                'email'         => 'test@edgeprod.com',
                'stage'         => '1',
                'campaign_id'   => '3',
                'creative_id'   => '106',
                'category_id'   => '7',
                'from_name'     => 'Potato Express',
                'sender_email'  => 'jason@matchquota.com',
                'subject'       => 'Fast and Easy Potatoes',
                'html_body'     => 'html body',
                'text_body'     => 'text body',
                'sub_id'        => '1',
                'channel'       => '1'
            ),
            array(
                'email'         => 'dom@leadwrench.com',
                'stage'         => '1',
                'campaign_id'   => '3',
                'creative_id'   => '106',
                'category_id'   => '7',
                'from_name'     => 'Potato Express',
                'sender_email'  => 'jason@matchquota.com',
                'subject'       => 'Fast and Easy Potatoes',
                'html_body'     => 'html body',
                'text_body'     => 'text body',
                'sub_id'        => '2',
                'channel'       => '1'
            ),
            array(
                'email'         => 'dom@edgeprod.com',
                'stage'         => '1',
                'campaign_id'   => '3',
                'creative_id'   => '107',
                'category_id'   => '8',
                'from_name'     => 'Potato Express BOGO',
                'sender_email'  => 'jason@matchquota.com',
                'subject'       => 'Fast and Easy Potatoes',
                'html_body'     => 'html body',
                'text_body'     => 'text body',
                'sub_id'        => '3',
                'channel'       => '1'
            ),
        ));
        
        SetupTestData::addThrottleData(array(
            array(
                'type'          => '4',
                'domain'        => 'edgeprod.com',
                'channel'       => '1',
                'creative_id'   => '106',
                'campaign_id'   => '3',
                'category_id'   => '7'
            ),
            array(
                'type'          => '4',
                'domain'        => 'leadwrench.com',
                'channel'       => '1',
                'creative_id'   => '100',
                'campaign_id'   => '5',
                'category_id'   => '1'
            )
        ));
        
        // execute function
        $leads = array(
            0 => array('build_queue_id' => '1'),
            1 => array('build_queue_id' => '2'),
            2 => array('build_queue_id' => '3')
        );
        
        $startTime = time();
        Engine_Scheduler::moveRecordsFromBuildQueueToSendQueue($leads);
        $send = new Queue_Send(1);
        $delayUntil = $send->getDelayUntil();
        
        // compare results
        if (! is_null($delayUntil) && intval($delayUntil) > 0) {
            $this->assertEqual(Config::SOFT_BOUNCE_DELAY_SECONDS, strtotime($delayUntil) - $startTime);
        } else {
            $this->assertEqual(1, 2);
        }
    }
    //--------------------------------------------------------------------------
    
    
    public function testThrotleComplaint()
    {
        // reset data
        SetupTestData::resetQueueBuildData();
        SetupTestData::resetQueueSendData();
        SetupTestData::resetThrottleData();
        
        // add test data
        $queueBuildData = array(
            array(
                'email'         => 'test@edgeprod.com',
                'stage'         => '1',
                'campaign_id'   => '3',
                'creative_id'   => '106',
                'category_id'   => '7',
                'from_name'     => 'Potato Express',
                'sender_email'  => 'jason@matchquota.com',
                'subject'       => 'Fast and Easy Potatoes',
                'html_body'     => 'html body',
                'text_body'     => 'text body',
                'sub_id'        => '1',
                'channel'       => '1'
            ),
            array(
                'email'         => 'dom@leadwrench.com',
                'stage'         => '1',
                'campaign_id'   => '3',
                'creative_id'   => '106',
                'category_id'   => '7',
                'from_name'     => 'Potato Express',
                'sender_email'  => 'jason@matchquota.com',
                'subject'       => 'Fast and Easy Potatoes',
                'html_body'     => 'html body',
                'text_body'     => 'text body',
                'sub_id'        => '2',
                'channel'       => '1'
            ),
            array(
                'email'         => 'dom@edgeprod.com',
                'stage'         => '1',
                'campaign_id'   => '3',
                'creative_id'   => '107',
                'category_id'   => '8',
                'from_name'     => 'Potato Express BOGO',
                'sender_email'  => 'jason@matchquota.com',
                'subject'       => 'Fast and Easy Potatoes',
                'html_body'     => 'html body',
                'text_body'     => 'text body',
                'sub_id'        => '3',
                'channel'       => '1'
            ),
        );
        SetupTestData::addQueueBuildData($queueBuildData);
        
        SetupTestData::addThrottleData(array(
            array(
                'type'          => '5',
                'domain'        => 'edgeprod.com',
                'channel'       => '1',
                'creative_id'   => '106',
                'campaign_id'   => '3',
                'category_id'   => '7'
            ),
            array(
                'type'          => '4',
                'domain'        => 'leadwrench.com',
                'channel'       => '1',
                'creative_id'   => '100',
                'campaign_id'   => '5',
                'category_id'   => '1'
            )
        ));
        
        // execute function
        $leads = array(
            0 => array('build_queue_id' => '1'),
            1 => array('build_queue_id' => '2'),
            2 => array('build_queue_id' => '3')
        );
        
        $startTime = date('Y-m-d H:i:s');
        Engine_Scheduler::moveRecordsFromBuildQueueToSendQueue($leads);
        
        $queueSends = Queue_Send::getUnlockedRowIds(1000);
        
        if (count($queueSends) < count($queueBuildData)) {
            $this->assertEqual(1, 1);
        } else {
            $this->assertEqual(1, 2);
        }
    }
    //--------------------------------------------------------------------------
}