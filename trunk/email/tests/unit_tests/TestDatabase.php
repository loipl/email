<?php

$skipOtherTests = 1;
defined('RUN_ALL_TESTS') or require_once '../tests.php';

class TestDatabase extends UnitTestCase
{

    public function testGetSender()
    {
        $db = new Database;

        $result = $db->getUpperLeft('SELECT `domain` FROM `senders` WHERE `id` = \'1\'');

        $this->assertNotNull($result);
    }
    //--------------------------------------------------------------------------


    public function testGetLead()
    {
        $db = new Database;

        $result = $db->getUpperLeft('SELECT `email` FROM `leads` LIMIT 1');

        $this->assertNotNull($result);
    }
    //--------------------------------------------------------------------------


    public function testGetCreative()
    {
        $db = new Database;

        $result = $db->getUpperLeft('SELECT `name` FROM `creatives` WHERE `id` = \'1\'');

        $this->assertNotNull($result);
    }
    //--------------------------------------------------------------------------


    public function testGetArray()
    {
        $db = new Database;

        $result = $db->getArray('SELECT `id`,`name` FROM `senders` WHERE `id` = \'1\'');

        $this->assertTrue(is_array($result));
    }
    //--------------------------------------------------------------------------


    public function testGetAssoc()
    {
        $db = new Database;

        $result = $db->getArrayAssoc('SELECT `id`,`name`,`domain`,`footer_id` FROM `senders` WHERE `id` = \'1\'');

        $this->assertTrue(is_array($result));
        $this->assertNotNull($result['id']);
        $this->assertNotNull($result['name']);
        $this->assertNotNull($result['domain']);
        $this->assertNotNull($result['footer_id']);
    }
    //--------------------------------------------------------------------------
}