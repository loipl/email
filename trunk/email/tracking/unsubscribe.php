<?php

require_once dirname(__FILE__) . '/../email.php';
require_once dirname(__FILE__) . '/EmailTracking.php';

class UnsubscribeEmailTracking extends EmailTracking
{

	public function __construct($parameterHolders = NULL , $parameterNames = array())
	{
		parent::__construct($parameterHolders, $parameterNames);
	}
    //--------------------------------------------------------------------------


	public function getManualUnsubscribeUrl()
	{
		return Config::$installedPath . '/tracking/manual-unsubscribe.php';
	}
    //--------------------------------------------------------------------------


	public function addSuppression()
	{
        $leadId = HTML::decodeHash($this->parameters['id']);

        if (isset($this->parameters['sub'])) {
            $subId  = HTML::decodeHash($this->parameters['sub']);
        }

        if ($leadId === false || !filter_var($leadId, FILTER_VALIDATE_EMAIL)) {
            $this->redirectToManualUnsubscribe();
            die();
        }

        Lead::addUnsubscribe($leadId);

        if (isset($subId)) {
            Lead::addUnsubscribeTransaction($leadId, $subId);
        }

        Lead::scoreUnsubscribe($leadId);

        $this->redirect();
	}
    //--------------------------------------------------------------------------


	public function redirect()
	{
        if (HTML::getUnsubscribeUrl()) {
		    header("Location: " . HTML::getUnsubscribeUrl());
        } else {
            die(Config::$unsubscribeText);
        }
	}
    //--------------------------------------------------------------------------


	public function redirectToManualUnsubscribe()
	{
		header("Location: {$this->getManualUnsubscribeUrl()}");
	}
    //--------------------------------------------------------------------------
}

$clickEmailTracking = new UnsubscribeEmailTracking($_GET , array('id', 'sub'));
$clickEmailTracking->addSuppression();

die();