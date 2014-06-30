<?php

require_once dirname(__FILE__) . '/../email.php';

Cron::checkConcurrency('maintain-locks');

$db = new Database;

$leads = Lead::getLeadsWithLocks();

if (is_array($leads)) {
    $sql  = "UPDATE `leads` SET `lock_id` = NULL, `lock_datetime` = NULL";
    $sql .= " WHERE `email` IN (";

    foreach($leads AS $lead) {
        $sql .= "'" . mysql_real_escape_string($lead['email']) . "',";
    }

    $sql = substr($sql, 0, -1);
    $sql .= ")";

    $db->query($sql);
}

$campaigns = Campaign::getCampaignsWithLocks();

if (is_array($campaigns)) {
    $sql  = "UPDATE `campaigns` SET `locked` = '0', `lock_datetime` = NULL";
    $sql .= " WHERE `id` IN (";

    foreach($campaigns AS $campaign) {
        $sql .= "'" . mysql_real_escape_string($campaign['id']) . "',";
    }

    $sql = substr($sql, 0, -1);
    $sql .= ")";

    $db->query($sql);
}

$crons = Locks_Cron::getCronsWithLocks();

if (is_array($crons)) {
    $sql  = "UPDATE `locks_cron` SET `locked` = '0', `datetime` = NULL";
    $sql .= " WHERE `identifier` IN (";

    foreach($crons AS $cron) {
        $sql .= "'" . mysql_real_escape_string($cron['identifier']) . "',";
    }

    $sql = substr($sql, 0, -1);
    $sql .= ")";

    $db->query($sql);
}

Locks_Cron::removeLock('maintain-locks');