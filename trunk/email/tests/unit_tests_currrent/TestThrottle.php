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
                'type'          => Config::TRANSACTION_TYPE_HARDBOUNCE,
                'domain'        => 'edgeprod.com',
                'channel'       => '1',
                'creative_id'   => '106',
                'campaign_id'   => '3',
                'category_id'   => '7'
            ),
            array(
                'type'          => Config::TRANSACTION_TYPE_SOFTBOUNCE,
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
                'type'          => Config::TRANSACTION_TYPE_SOFTBOUNCE,
                'domain'        => 'edgeprod.com',
                'channel'       => '1',
                'creative_id'   => '106',
                'campaign_id'   => '3',
                'category_id'   => '7'
            ),
            array(
                'type'          => Config::TRANSACTION_TYPE_SOFTBOUNCE,
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
                'type'          => Config::TRANSACTION_TYPE_COMPLAINT,
                'domain'        => 'edgeprod.com',
                'channel'       => '1',
                'creative_id'   => '106',
                'campaign_id'   => '3',
                'category_id'   => '7'
            ),
            array(
                'type'          => Config::TRANSACTION_TYPE_SOFTBOUNCE,
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
    
    
    public function testThrotleMultipleMatches()
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
                'type'          => Config::TRANSACTION_TYPE_HARDBOUNCE,
                'domain'        => 'edgeprod.com',
                'channel'       => '1',
                'creative_id'   => '106',
                'campaign_id'   => '3',
                'category_id'   => '7'
            ),
            array(
                'type'          => Config::TRANSACTION_TYPE_SOFTBOUNCE,
                'domain'        => 'leadwrench.com',
                'channel'       => '1',
                'creative_id'   => '100',
                'campaign_id'   => '5',
                'category_id'   => '1'
            ),
            array(
                'type'          => Config::TRANSACTION_TYPE_SOFTBOUNCE,
                'domain'        => 'edgeprod.com',
                'channel'       => '1',
                'creative_id'   => '106',
                'campaign_id'   => '3',
                'category_id'   => '7'
            ),
            array(
                'type'          => Config::TRANSACTION_TYPE_SOFTBOUNCE,
                'domain'        => 'edgeprod.com',
                'channel'       => '1',
                'creative_id'   => '107',
                'campaign_id'   => '3',
                'category_id'   => '8'
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
            $this->assertEqual(Config::HARD_BOUNCE_DELAY_SECONDS + 2 * Config::SOFT_BOUNCE_DELAY_SECONDS, strtotime($delayUntil) - $startTime);
        } else {
            $this->assertEqual(1, 2);
        }
    }
    //--------------------------------------------------------------------------
    
    
    public function testThrotleMultipleMatchesAndStackingDelay()
    {
        $stackDelaySeconds = 15;
        
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
        
        SetupTestData::addQueueSendData(array(
                array(
                    'email'         => 'dom@edgeprod.com',
                    'campaign_id'   => '3',
                    'creative_id'   => '107',
                    'category_id'   => '8',
                    'from_name'     => 'Potato Express BOGO',
                    'sender_email'  => 'jason@matchquota.com',
                    'subject'       => 'Fast and Easy Potatoes',
                    'html_body'     => 'html body',
                    'text_body'     => 'text body',
                    'sub_id'        => '3',
                    'channel'       => '1',
                    'delay_until'   => date('Y-m-d H:i:s', (time() + $stackDelaySeconds)),
                    'delay_seconds' => $stackDelaySeconds
                )
            )
        );
        
        SetupTestData::addThrottleData(array(
            array(
                'type'          => Config::TRANSACTION_TYPE_HARDBOUNCE,
                'domain'        => 'edgeprod.com',
                'channel'       => '1',
                'creative_id'   => '106',
                'campaign_id'   => '3',
                'category_id'   => '7'
            ),
            array(
                'type'          => Config::TRANSACTION_TYPE_SOFTBOUNCE,
                'domain'        => 'leadwrench.com',
                'channel'       => '1',
                'creative_id'   => '100',
                'campaign_id'   => '5',
                'category_id'   => '1'
            ),
            array(
                'type'          => Config::TRANSACTION_TYPE_SOFTBOUNCE,
                'domain'        => 'edgeprod.com',
                'channel'       => '1',
                'creative_id'   => '106',
                'campaign_id'   => '3',
                'category_id'   => '7'
            ),
            array(
                'type'          => Config::TRANSACTION_TYPE_SOFTBOUNCE,
                'domain'        => 'edgeprod.com',
                'channel'       => '1',
                'creative_id'   => '107',
                'campaign_id'   => '3',
                'category_id'   => '8'
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
        $send = new Queue_Send(2);
        $delayUntil = $send->getDelayUntil();
        
        // compare results
        if (! is_null($delayUntil) && intval($delayUntil) > 0) {
            $this->assertEqual(Config::HARD_BOUNCE_DELAY_SECONDS + 2 * Config::SOFT_BOUNCE_DELAY_SECONDS + $stackDelaySeconds, strtotime($delayUntil) - $startTime);
        } else {
            $this->assertEqual(1, 2);
        }
    }
    //--------------------------------------------------------------------------
    
    
    public function testThrottleSwithchingChannel()
    {
        // reset data
        SetupTestData::resetQueueBuildData();
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
                'channel'       => NULL
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
                'channel'       => NULL
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
                'channel'       => NULL
            ),
        ));
        
        SetupTestData::addThrottleData(array(
            array(
                'type'          => Config::TRANSACTION_TYPE_HARDBOUNCE,
                'domain'        => 'edgeprod.com',
                'channel'       => '1',
                'creative_id'   => '106',
                'campaign_id'   => '3',
                'category_id'   => '7'
            ),
            array(
                'type'          => Config::TRANSACTION_TYPE_SOFTBOUNCE,
                'domain'        => 'leadwrench.com',
                'channel'       => '1',
                'creative_id'   => '100',
                'campaign_id'   => '5',
                'category_id'   => '1'
            ),
            array(
                'type'          => Config::TRANSACTION_TYPE_SOFTBOUNCE,
                'domain'        => 'edgeprod.com',
                'channel'       => '1',
                'creative_id'   => '106',
                'campaign_id'   => '3',
                'category_id'   => '7'
            ),
            array(
                'type'          => Config::TRANSACTION_TYPE_SOFTBOUNCE,
                'domain'        => 'edgeprod.com',
                'channel'       => '2',
                'creative_id'   => '107',
                'campaign_id'   => '3',
                'category_id'   => '8'
            )
        ));
        
        // execute function
        $leads = array(
            0 => array('build_queue_id' => '1'),
            1 => array('build_queue_id' => '2'),
            2 => array('build_queue_id' => '3')
        );
        
        Engine_Scheduler_Channels::pushChannelsToBuildQueue($leads);
        
        $queue1 = new Queue_Build('1');
        $queue2 = new Queue_Build('2');
        $queue3 = new Queue_Build('3');
        
        $this->assertEqual($queue1->getChannel(), '2');
        $this->assertEqual($queue2->getChannel(), '2');
        $this->assertEqual($queue3->getChannel(), '1');
    }
    //--------------------------------------------------------------------------
}