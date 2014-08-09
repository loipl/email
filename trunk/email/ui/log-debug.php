<?php

require_once dirname(__FILE__) . '/../email.php';

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 100;

$startIndex = ($page - 1) * $limit;

$sql = "SELECT * from error_log_debug"
        . " ORDER BY id desc"
        . " LIMIT $startIndex, $limit";

$db = new Database();

$logs = $db->getArray($sql);
        
foreach ($logs as $log) {
    echo '<div style="margin-top:10px;">' . json_encode($log) . '</div>';
}
?>