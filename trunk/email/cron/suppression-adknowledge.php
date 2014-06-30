<?php

require_once dirname(__FILE__) . '/../email.php';

Cron::checkConcurrency('suppression-adknowledge');

$db = new Database;

$from = date('Y-m-d',(strtotime('-3 days', strtotime('now'))));
$now = date('Y-m-d', strtotime ('now'));

$url = 'http://api.publisher.adknowledge.com/adstation/integrated?token=7dcd340d84f762eba80aa538b0c527f7380f943f5baa057b7b3fd9ef58b13a10&action=getSuppressionList&format=csv&date_start=' . $from . '&date_end=' . $now;

$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_URL, $url);

$data = curl_exec($ch);

$output = explode("\n", $data);

foreach($output AS $line) {
    $csv = str_getcsv($line);

    if(isset($csv[2]) && $csv[2] == 'email') {
        Suppression_Email_Md5::addEmailMd5Suppression(mysql_real_escape_string($csv[0]),5,5);
    } elseif(isset($csv[2]) && $csv[2] == 'domain') {
        Suppression_Domain_Md5::addDomainMd5Suppression(mysql_real_escape_string($csv[0]),5,5);
    } else {
        continue;
    }
}

curl_close($ch);

Locks_Cron::removeLock('suppression-adknowledge');