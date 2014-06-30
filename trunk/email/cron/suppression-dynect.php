<?php

require_once dirname(__FILE__) . '/../email.php';

Cron::checkConcurrency('suppression-dynect');

$dyn = new Dynect;
$db = new Database;

$from = date('Y-m-d',(strtotime('-1 days', strtotime('now'))));
$now = date('Y-m-d', strtotime ('now'));

$bounces = $dyn->getBounceList($from, $now, 'hard');

foreach ($bounces AS $record) {
    Suppression_Email::addEmailSuppression(mysql_real_escape_string($record->{'emailaddress'}), 3, 1);
    Lead::scoreHardBounce($record->{'emailaddress'});

    if (isset($record->{'xheaders'}->{'X-Activity-ID'}) && $record->{'xheaders'}->{'X-Activity-ID'} > 0) {
        if (Transaction::checkTransactionExists(6, $record->{'emailaddress'}, $record->{'xheaders'}->{'X-Activity-ID'}) === false) {
            $sql  = 'INSERT INTO `transactions` (id, type, email, campaign_id, creative_id, datetime, activity_id) VALUES (';
            $sql .= 'NULL,';
            $sql .= ' \'6\',';
            $sql .= ' \'' .mysql_real_escape_string($record->{'emailaddress'}). '\',';
            $sql .= ' NULL,';
            $sql .= ' NULL,';
            $sql .= ' NOW(), ';
            $sql .= ' \'' .mysql_real_escape_string($record->{'xheaders'}->{'X-Activity-ID'}). '\'';
            $sql .= ')';

            $db->query($sql);
        }
    }
}

$previousBounces = $dyn->getBounceList($from, $now, 'previouslyhardbounced');

foreach ($previousBounces AS $record) {
    Suppression_Email::addEmailSuppression(mysql_real_escape_string($record->{'emailaddress'}), 3, 2);
    Lead::scoreHardBounce($record->{'emailaddress'});
}

$complaints = $dyn->getComplaintList($from, $now);

foreach ($complaints AS $record) {
    Suppression_Email::addEmailSuppression(mysql_real_escape_string($record->{'emailaddress'}), 3, 3);
    Lead::scoreComplaint($record->{'emailaddress'});

    if (isset($record->{'xheaders'}->{'X-Activity-ID'}) && $record->{'xheaders'}->{'X-Activity-ID'} > 0) {
        if (Transaction::checkTransactionExists(5, $record->{'emailaddress'}, $record->{'xheaders'}->{'X-Activity-ID'}) === false) {
            $sql  = 'INSERT INTO `transactions` (id, type, email, campaign_id, creative_id, datetime, activity_id) VALUES (';
            $sql .= 'NULL,';
            $sql .= ' \'5\',';
            $sql .= ' \'' .mysql_real_escape_string($record->{'emailaddress'}). '\',';
            $sql .= ' NULL,';
            $sql .= ' NULL,';
            $sql .= ' NOW(), ';
            $sql .= ' \'' .mysql_real_escape_string($record->{'xheaders'}->{'X-Activity-ID'}). '\'';
            $sql .= ')';

            $db->query($sql);
        }
    }
}

$previousComplaints = $dyn->getBounceList($from, $now, 'previouslycomplained');

foreach ($previousComplaints AS $record) {
    Suppression_Email::addEmailSuppression(mysql_real_escape_string($record->{'emailaddress'}), 3, 4);
    Lead::scoreComplaint($record->{'emailaddress'});
}

$softBounces = $dyn->getBounceList($from, $now, 'soft');

foreach ($softBounces AS $record) {
    if (isset($record->{'xheaders'}->{'X-Activity-ID'}) && $record->{'xheaders'}->{'X-Activity-ID'} > 0) {
        if (Transaction::checkTransactionExists(4, $record->{'emailaddress'}, $record->{'xheaders'}->{'X-Activity-ID'}) === false) {
            $sql  = 'INSERT INTO `transactions` (id, type, email, campaign_id, creative_id, datetime, activity_id) VALUES (';
            $sql .= 'NULL,';
            $sql .= ' \'4\',';
            $sql .= ' \'' .mysql_real_escape_string($record->{'emailaddress'}). '\',';
            $sql .= ' NULL,';
            $sql .= ' NULL,';
            $sql .= ' NOW(), ';
            $sql .= ' \'' .mysql_real_escape_string($record->{'xheaders'}->{'X-Activity-ID'}). '\'';
            $sql .= ')';

            $db->query($sql);
        }
    }

    Lead::scoreSoftBounce($record->{'emailaddress'});
}

Locks_Cron::removeLock('suppression-dynect');