<?php

$skipOtherTests = 1;
defined('RUN_ALL_TESTS') or require_once '../tests.php';

class TestDependencies extends UnitTestCase
{

    public function testPHPVersion()
    {
        $this->assertTrue(phpversion() > '5.2.0');
    }
    //--------------------------------------------------------------------------


    public function testMySQLVersion()
    {
        $db = new Database;

        $this->assertTrue($db->getMySQLVersion() > '5.5.0');
    }
    //--------------------------------------------------------------------------


    public function testcURLEnabled()
    {
        $this->assertTrue(function_exists('curl_version'));
    }
    //--------------------------------------------------------------------------
}