<?php

require_once dirname(__FILE__) . '/../email.php';

Cron::checkConcurrency('generate-statistics');

$db = new Database;

switch ($argv[1]) {
    case 'daily' :
        Statistic::cronDailyProcess();
        break;

    case 'hourly' :
        Statistic::cronHourlyProcess();
        break;

    case 'minutely' :
        Statistic::cronMinutelyProcess();
        break;
}

Locks_Cron::removeLock('generate-statistics');
