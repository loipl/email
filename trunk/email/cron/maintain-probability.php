<?php

require_once dirname(__FILE__) . '/../email.php';

Cron::checkConcurrency('maintain-probability');

$db = new Database;

$expiredRows = Probability::getExpiredRows();

if (is_array($expiredRows)) {
    $sql  = "DELETE FROM `probability`";
    $sql .= " WHERE `id` IN (";

    foreach($expiredRows AS $row) {
        $sql .= "'" . mysql_real_escape_string($row['id']) . "',";
    }

    $sql = substr($sql, 0, -1);
    $sql .= ")";

    $db->query($sql);
}

Locks_Cron::removeLock('maintain-probability');