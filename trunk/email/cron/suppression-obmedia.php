<?php

require_once dirname(__FILE__) . '/../email.php';

Cron::checkConcurrency('suppression-obmedia');

$db = new Database;

$url = 'https://obmedia.com/m/feed/suppressionListDownload.php/?apiKey=ac64aacc-8624-4895-a50d-eae0709c174a&type=30dayNonZip';

$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_URL, $url);

$data = curl_exec($ch);

$output = explode("\n", $data);

foreach ($output AS $line) {
    $line = trim($line);

    if(!empty($line)) {
        Suppression_Email_Md5::addEmailMd5Suppression(mysql_real_escape_string($line),6,5);
    } else {
        continue;
    }
}

curl_close($ch);

Locks_Cron::removeLock('suppression-obmedia');