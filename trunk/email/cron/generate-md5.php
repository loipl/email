<?php

require_once dirname(__FILE__) . '/../email.php';

Cron::checkConcurrency('generate-md5');

$leads = Lead::getLeadsWithoutMD5(1000);

if (is_array($leads)) {
    foreach ($leads AS $lead) {

        $emailParts = explode('@',strtolower(trim($lead['email'])));

        $emailMD5  = md5(strtolower($lead['email']));
        $domainMD5 = md5($emailParts[1]);

        Lead::updateMD5($lead['email'], $emailMD5, $domainMD5);
    }
}

Locks_Cron::removeLock('generate-md5');