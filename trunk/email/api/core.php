<?php

require_once dirname(__FILE__) . '/../email.php';

if (empty($_SERVER['PHP_AUTH_USER']) && !isset($_GET['apikey'])) {
    die("Invalid API key");
}

if (isset($_SERVER['PHP_AUTH_USER'])) {
    if (($_SERVER['PHP_AUTH_USER'] != Config::$apiKey) && ($_GET['apikey'] != Config::$apiKey)) {
        die("Invalid API key");
    }
}
