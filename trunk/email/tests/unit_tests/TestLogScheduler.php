<?php

$skipOtherTests = 1;
defined('RUN_ALL_TESTS') or require_once '../tests.php';

class TestLogScheduler extends UnitTestCase
{
    
    public function setUp()
    {
        SetupTestData::resetLogScheduler();
        LogScheduler::init();
    }
    
    public function testInit()
    {
        $id = LogScheduler::getId();
        $this->assertEqual($id, '1');
    }
    
    public function testSave() {
        $newAttributes = array(
            'eligible_campaign_count' => 1,
            'eligible_campaign_ids' => serialize(array(1,2,3))
        );
        LogScheduler::addAttributes($newAttributes);
        LogScheduler::save();
        
        $id = LogScheduler::getId();
        $dbData = LogScheduler::getById($id);
        $logData = $dbData[0];
        $this->assertEqual($logData['eligible_campaign_count'], '1');
        $this->assertEqual($logData['eligible_campaign_ids'], serialize(array(1,2,3)));
    }
    
    public function testReset() {
        LogScheduler::reset();
        $id = LogScheduler::getId();
        $this->assertEqual($id, null);
    }
    
    public function testScheduler() {
        SetupTestData::resetLogScheduler();
        new Engine_Scheduler('diepbuihuu@gmail.com', '3', 'scheduler-1');

        $dbData = LogScheduler::getById(1);
        $logData = $dbData[0];
        $this->assertEqual($logData["scheduler_name"], 'scheduler-1');     
        $this->assertEqual($logData["chosen_campaign_id"], '3');     
        $this->assertEqual($logData["lead_count"], '1');     
        $this->assertEqual($logData["leads"], serialize(array(array('email' => 'diepbuihuu@gmail.com'))));     
    }
}