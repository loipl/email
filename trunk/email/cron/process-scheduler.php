<?php

require_once dirname(__FILE__) . '/../email.php';

if (isset($argv[1])) {
    sleep($argv[1]);
}

if (isset($argv[2])) {
    Cron::checkConcurrency($argv[2]);
} else {
    Cron::checkConcurrency('process-scheduler');
}

if (isset($argv[2])) {
    $scheduler = new Engine_Scheduler(NULL, NULL, $argv[2]);
} else {
    $scheduler = new Engine_Scheduler(NULL, NULL, 'process-scheduler');
}

if (isset($argv[2])) {
    Locks_Cron::removeLock($argv[2]);
} else {
    Locks_Cron::removeLock('process-scheduler');
}