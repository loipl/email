<?php

$skipOtherTests = 1;
defined('RUN_ALL_TESTS') or require_once '../tests.php';

class TestBasics extends UnitTestCase
{

    public function testFromDomainsNotNull()
    {
        $this->assertNotNull(array_merge(
            TldList::$aolTldList,
            TldList::$microsoftTldList,
            TldList::$gmailTldList,
            TldList::$unitedOnlineTldList,
            TldList::$cableTldList,
            TldList::$yahooTldList));
    }
    //--------------------------------------------------------------------------


    public function testAPIKeyNotNull()
    {
        $this->assertNotNull(Config::$apiKey);
    }
    //--------------------------------------------------------------------------


    public function testErrorHandlerExists()
    {
        $this->assertTrue(function_exists('coreErrorHandler'));
    }
    //--------------------------------------------------------------------------
}