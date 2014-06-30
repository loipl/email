<?php

require_once dirname(__FILE__) . '/core.php';

$validMetrics   = array_values(Config::$validMetrics);
$validTypes     = array_values(Config::$validTypes);
$validIntervals = Config::$validIntervals;

if (Dates::validateDate($_GET['start']) === false || !isset($_GET['start'])) {
    die('ERROR: Missing or invalid start date');
}

if (Dates::validateDate($_GET['end']) === false || !isset($_GET['end'])) {
    die('ERROR: Missing or invalid end date');
}

if (!isset($_GET['metric']) || !in_array($_GET['metric'], $validMetrics)) {
    die('ERROR: Missing or invalid metric');
}

if (!isset($_GET['type']) || !in_array($_GET['type'], $validTypes)) {
    die('ERROR: Missing or invalid type');
}

if (isset($_GET['interval'])) {
    if (!in_array($_GET['interval'], $validIntervals)) {
        die ('ERROR: Invalid interval');
    }
}

$interval = (isset($_GET['interval']) ? $_GET['interval'] : null);
$metric   = (isset($_GET['metric'])   ? $_GET['metric']   : null);
$start    = (isset($_GET['start'])    ? $_GET['start']    : null);
$type     = (isset($_GET['type'])     ? $_GET['type']     : null);
$end      = (isset($_GET['end'])      ? $_GET['end']      : null);
$id       = (isset($_GET['id'])       ? $_GET['id']       : null);

$data = Statistic::getTotals($metric, $type, $id, $interval, $start, $end);

$output = json_encode($data);

if ($output == 'false') {
    echo "No data or reporting error.";
} else {
    echo $output;
}
