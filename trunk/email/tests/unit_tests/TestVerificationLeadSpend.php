<?php

$skipOtherTests = 1;
defined('RUN_ALL_TESTS') or require_once '../tests.php';

class TestVerificationLeadSpend extends UnitTestCase
{

    public function testWithoutParameters()
    {
        $leadSpend = new LeadSpend;
        $leadSpend->processLead('test@example.com');
        $this->assertNotNull($leadSpend->getRawResponse());
        $parsedResponse = $leadSpend->getParsedResponse();
        $this->assertTrue(is_array($parsedResponse) && !empty($parsedResponse));
        $this->assertTrue($leadSpend->isParsed());
        $this->assertNotNull($leadSpend->getResult());

        $leadSpend = new LeadSpend;
        $leadSpend->processLead('invalid email');
        $this->assertNotNull($leadSpend->getRawResponse());
        $parsedResponse = $leadSpend->getParsedResponse();
        $this->assertTrue(is_array($parsedResponse) && !empty($parsedResponse));
        $this->assertTrue($leadSpend->isParsed());
        $this->assertTrue($leadSpend->getResult() === 'undeliverable');

        $leadSpend = new LeadSpend;
        $leadSpend->processLead('');
        $this->assertNull($leadSpend->getRawResponse());
        $parsedResponse = $leadSpend->getParsedResponse();
        $this->assertTrue(is_array($parsedResponse) && empty($parsedResponse));
        $this->assertFalse($leadSpend->isParsed());
        $this->assertNull($leadSpend->getResult());
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
            'optinsite' => 'www.leadspend.com',
            'optindate' => '2010-02-10',
            'optinip' => '34.44.55.32',
            'other1' => '',
            'other2' => null,
        );

        $leadSpend = new LeadSpend;
        $leadSpend->processLead('test@example.com', $params);
        $this->assertNotNull($leadSpend->getRawResponse());
        $parsedResponse = $leadSpend->getParsedResponse();
        $this->assertTrue(is_array($parsedResponse) && !empty($parsedResponse));
        $this->assertTrue($leadSpend->isParsed());
        $this->assertNotNull($leadSpend->getResult());

        $leadSpend = new LeadSpend;
        $leadSpend->processLead('invalid email', $params);
        $this->assertNotNull($leadSpend->getRawResponse());
        $parsedResponse = $leadSpend->getParsedResponse();
        $this->assertTrue(is_array($parsedResponse) && !empty($parsedResponse));
        $this->assertTrue($leadSpend->isParsed());
        $this->assertTrue($leadSpend->getResult() === 'undeliverable');

        $leadSpend = new LeadSpend;
        $leadSpend->processLead('', $params);
        $this->assertNull($leadSpend->getRawResponse());
        $parsedResponse = $leadSpend->getParsedResponse();
        $this->assertTrue(is_array($parsedResponse) && empty($parsedResponse));
        $this->assertFalse($leadSpend->isParsed());
        $this->assertNull($leadSpend->getResult());
    }
    //--------------------------------------------------------------------------
}