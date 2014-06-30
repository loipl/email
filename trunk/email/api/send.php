<?php

require_once dirname(__FILE__) . '/core.php';

if (isset($_GET['email']) && isset($_GET['campaign_id'])) {
    if (!filter_var($_GET['email'], FILTER_VALIDATE_EMAIL)) {
        die("SEND REJECTED: The email address provided is invalid.");
    }

    if (Lead::isSuppressed($_GET['email'])) {
        die("SEND REJECTED: The email address provided is in the suppression list.");
    }

    $scheduler = new Engine_Scheduler($_GET['email'], $_GET['campaign_id']);

    echo "SEND ACCEPTED: Lead queued for sending";
} else {
    die("SEND REJECTED: Email and/or campaign_id not specified");
}
