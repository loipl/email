<?php

require_once dirname(__FILE__) . '/../email.php';

Cron::checkConcurrency('hygiene-impressionwise');

$hygiene = new ImpressionWise();

$leadAttributes = array(
    'countOnly'  => false,
    'queryName'  => false,
    'count'      => Config::MAX_BATCH_SIZE,
    'interval'   => NULL,
    'type'       => 'verified',
    'minScore'   => '1',
    'campaignId' => NULL,
    'tldList'    => array_merge(
        TldList::$aolTldList,
        TldList::$microsoftTldList,
        TldList::$gmailTldList,
        TldList::$unitedOnlineTldList,
        TldList::$cableTldList,
        TldList::$yahooTldList),
    'hygiene'    => false,
    'gender'     => NULL,
    'country'    => array('US','CA','GB'),
    'state'      => NULL,
    'inverse'    => array('tldList' => true)
);

$lead = new Lead();
$leads = $lead->getLeads(new Database, $leadAttributes);

if (empty($leads)) {
    Locks_Cron::removeLock('hygiene-impressionwise');
    die();
}

foreach ($leads AS $lead) {
    $hygiene->processLead($lead['email']);
    $decision = $hygiene->getDecision();

    switch ($decision) {
        case 'validemail' :
            Lead::setHygieneDatetime($lead['email']);
            continue;
        break;

        case 'invalidemail' :
            Lead::setHygieneDatetime($lead['email']);
            Suppression_Email::addEmailSuppression($lead['email'], 7, 7);
            Transaction::addTransaction($lead['email'], 7);
            Lead::scoreHygieneFail($lead['email']);
            break;

        case 'invaliddomain' :
            Lead::setHygieneDatetime($lead['email']);
            Suppression_Domain::addDomainSuppression($lead['domain'], 7, 7);
            Transaction::addTransaction($lead['email'], 7);
            Lead::scoreHygieneFail($lead['email']);
            break;

        case 'retry' :
            sleep(5);

            $hygiene->processLead($lead['email']);
            $decision = $hygiene->getDecision();

            if ($decision == 'validemail') {
                Lead::setHygieneDatetime($lead['email']);
                continue;
            } else if ($decision == 'invalidemail') {
                Lead::setHygieneDatetime($lead['email']);
                Suppression_Email::addEmailSuppression($lead['email'], 7, 7);
                Transaction::addTransaction($lead['email'], 7);
                Lead::scoreHygieneFail($lead['email']);
            } else if ($decision == 'invaliddomain') {
                Lead::setHygieneDatetime($lead['email']);
                Suppression_Domain::addDomainSuppression($lead['domain'], 7, 7);
                Transaction::addTransaction($lead['email'], 7);
                Lead::scoreHygieneFail($lead['email']);
            }
        break;
    }
}

Locks_Cron::removeLock('hygiene-impressionwise');
