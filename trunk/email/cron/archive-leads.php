<?php

require_once dirname(__FILE__) . '/../email.php';

Cron::checkConcurrency('archive-leads');

$db = new Database;

$sql  = "SELECT `email` FROM `leads` WHERE `score` = '0' LIMIT 1000";
$result = $db->getArray($sql);

foreach ($result AS $record) {
    Lead::archiveLead($db, $record['email']);
}

Locks_Cron::removeLock('archive-leads');
