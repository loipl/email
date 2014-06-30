<?php

require_once dirname(__FILE__) . '/../email.php';

Cron::checkConcurrency('generate-domains');

$db = new Database;

$leads = Lead::getLeadsWithoutDomain('50000');

if (is_array($leads)) {
    foreach($leads AS $lead) {
        $domain = explode('@',$lead['email']);

        $sql  = "UPDATE `leads` SET `domain` = '" . mysql_real_escape_string($domain[1]) . "'";
        $sql .= " WHERE `email` = '" . mysql_real_escape_string($lead['email']) . "' LIMIT 1";

        $db->query($sql);
    }
}

Locks_Cron::removeLock('generate-domains');