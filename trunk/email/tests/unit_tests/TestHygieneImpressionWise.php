<?php

$skipOtherTests = 1;
defined('RUN_ALL_TESTS') or require_once '../tests.php';

class TestHygieneImpressionWise extends UnitTestCase
{

    public function testWithoutParameters()
    {
        $impressionWise = new ImpressionWise;
        $impressionWise->processLead('test@example.com');
        $this->assertNotNull($impressionWise->getRawResponse());
        $parsedResponse = $impressionWise->getParsedResponse();
        $this->assertTrue(is_array($parsedResponse) && !empty($parsedResponse));
        $this->assertTrue($impressionWise->isParsed());
        $this->assertNotNull($impressionWise->getNPD());
        $this->assertNotNull($impressionWise->getResult());
        $this->assertNotNull($impressionWise->getTTP());

        $impressionWise = new ImpressionWise;
        $impressionWise->processLead('invalid email');
        $this->assertNotNull($impressionWise->getRawResponse());
        $parsedResponse = $impressionWise->getParsedResponse();
        $this->assertTrue(is_array($parsedResponse) && !empty($parsedResponse));
        $this->assertTrue($impressionWise->isParsed());
        $this->assertNotNull($impressionWise->getNPD());
        $this->assertTrue($impressionWise->getResult() === 'Invalid');
        $this->assertNotNull($impressionWise->getTTP());

        $impressionWise = new ImpressionWise;
        $impressionWise->processLead('');
        $this->assertNull($impressionWise->getRawResponse());
        $parsedResponse = $impressionWise->getParsedResponse();
        $this->assertTrue(is_array($parsedResponse) && empty($parsedResponse));
        $this->assertFalse($impressionWise->isParsed());
        $this->assertNull($impressionWise->getNPD());
        $this->assertNull($impressionWise->getResult());
        $this->assertNull($impressionWise->getTTP());
    }
    //--------------------------------------------------------------------------


    public function testWithParameters()
    {
        $params = array(
            'fname' => 'test',
            'lname' => 'me',
            'addr1' => '123 sw 150 ave',
            'addr2' => '',
            'city' => 'miami',
            'state' => 'FL',
            'zip' => '33222',
            'country' => 'USA',
            'phone' => '3057645678',
            'gender' => 'm',
            'optinsite' => 'www.impressionwise.com',
            'optindate' => '2010-02-10',
            'optinip' => '34.44.55.32',
            'other1' => '',
            'other2' => null,
        );

        $impressionWise = new ImpressionWise;
        $impressionWise->processLead('test@example.com', $params);
        $this->assertNotNull($impressionWise->getRawResponse());
        $parsedResponse = $impressionWise->getParsedResponse();
        $this->assertTrue(is_array($parsedResponse) && !empty($parsedResponse));
        $this->assertTrue($impressionWise->isParsed());
        $this->assertNotNull($impressionWise->getNPD());
        $this->assertNotNull($impressionWise->getResult());
        $this->assertNotNull($impressionWise->getTTP());

        $impressionWise = new ImpressionWise;
        $impressionWise->processLead('invalid email', $params);
        $this->assertNotNull($impressionWise->getRawResponse());
        $parsedResponse = $impressionWise->getParsedResponse();
        $this->assertTrue(is_array($parsedResponse) && !empty($parsedResponse));
        $this->assertTrue($impressionWise->isParsed());
        $this->assertNotNull($impressionWise->getNPD());
        $this->assertTrue($impressionWise->getResult() === 'Invalid');
        $this->assertNotNull($impressionWise->getTTP());

        $impressionWise = new ImpressionWise;
        $impressionWise->processLead('', $params);
        $this->assertNull($impressionWise->getRawResponse());
        $parsedResponse = $impressionWise->getParsedResponse();
        $this->assertTrue(is_array($parsedResponse) && empty($parsedResponse));
        $this->assertFalse($impressionWise->isParsed());
        $this->assertNull($impressionWise->getNPD());
        $this->assertNull($impressionWise->getResult());
        $this->assertNull($impressionWise->getTTP());
    }
    //--------------------------------------------------------------------------
}