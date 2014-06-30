<?php

$skipOtherTests = 1;
defined('RUN_ALL_TESTS') or require_once '../tests.php';

class TestDataAppendRapleaf extends UnitTestCase
{

    public function testRapleafName()
    {
        $rapleaf = new Rapleaf;

        $this->assertEqual($rapleaf->getName(),'Rapleaf');
    }
    //--------------------------------------------------------------------------
}