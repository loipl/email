<?php

require_once dirname(__FILE__) . '/../email.php';

Cron::checkConcurrency('suppression-sendgrid');

$sendgrid = new Sendgrid;

$from = date('Y-m-d',(strtotime('-1 days', strtotime('now'))));
$now = date('Y-m-d', strtotime ('now'));

$spamReports = $sendgrid->getSpamList($from, $now);
$bounces = $sendgrid->getBounces($from, $now);

Locks_Cron::removeLock('suppression-sendgrid');