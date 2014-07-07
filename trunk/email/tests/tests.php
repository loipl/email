<?php

define('TEST_START_TIME', microtime(true));
define('RUN_ALL_TESTS', 1);
$_SERVER['test_environment'] = true;

chdir(dirname(__FILE__) . '/');

$excludeTests = getSkipList();

if (!isset($skipOtherTests)) {
    $skipOtherTests = false;
}

require_once dirname(__FILE__) . '/simpletest/autorun.php';
require_once dirname(dirname(__FILE__)) . '/email.php';
require_once dirname(__FILE__) . '/SetupTestData.php';

if (empty($skipOtherTests)) {
    foreach (Utilities::listFiles(dirname(__FILE__) . '/unit_tests/', true) as $fileName) {
        if (substr($fileName, -4) == '.php') {
            if (isset($excludeTests) && is_array($excludeTests)) {
                if (!in_array(basename($fileName, '.php'), $excludeTests)) {
                    require_once($fileName);
                }
            } else {
                require_once($fileName);
            }
        }
    }
}

echo number_format(microtime(true) - TEST_START_TIME, 4) . "s startup time\n";

function getSkipList() {
    if (!isset($_GET['skip'])) {
        return false;
    }

    $skippedTests = explode('-',$_GET['skip']);

    if (!empty($skippedTests)) {
        return $skippedTests;
    } else {
        return false;
    }
}