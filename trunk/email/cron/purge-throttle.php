<?php

require_once dirname(__FILE__) . '/../email.php';

Cron::checkConcurrency('purge-throttle');

// remove throttle that older than X minutes (set in Config.php)
Throttle::purgeThrottle();

Locks_Cron::removeLock('purge-throttle');