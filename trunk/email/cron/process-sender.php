<?php

require_once dirname(__FILE__) . '/../email.php';

if (isset($argv[1])) {
    sleep($argv[1]);
}

if (isset($argv[3])) {
    Cron::checkConcurrency($argv[3]);
} else {
Cron::checkConcurrency('process-sender');
}

$sender = new Engine_Sender();

if (isset($argv[2])) {
    $sender->processSendQueue($argv[2]);
} else {
    $sender->processSendQueue(1000);
}

if (isset($argv[3])) {
    Locks_Cron::removeLock($argv[3]);
} else {
Locks_Cron::removeLock('process-sender');
}