<?php

require_once dirname(__FILE__) . '/../email.php';

Cron::checkConcurrency('maintain-leads');

// Removes suppressed domains from leads

Locks_Cron::removeLock('maintain-leads');