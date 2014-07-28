#!/bin/env php
<?php

include_once dirname(__FILE__) . '/../Config.php';
include_once dirname(__FILE__) . '/../cron/statistics.php';

date_default_timezone_set(Config::$db_timezone);

class Stats_aggregation_test extends Stats {
    const NUM_TEST_RUNS = 5;

    private $test_failures = 0;

    public function __construct() {
        $this->connect_db();
    }

    public function run_tests() {
        echo "Staring to run tests ...";

        $this->test_minutely_data();
        $this->test_hourly_data();
        $this->test_daily_data();
        $this->test_monthly_data();

        if ($this->test_failures > 0) {
            $msg = "Tests have failed: (" . $this->test_failures . " test failures)";
            throw new Exception($msg);
        }

        return true;
    }

    private function generate_test_filters() {
        $filters = array(
            'metric' => rand(1, 5),
            'year'   => 2014,
            'month'  => rand(1, 4),
            'day'    => rand(1, 31),
            'hour'   => rand(1, 23),
            'minute' => rand(1, 59)
        );

        return $filters;
    }

    private function test_minutely_data() {
        echo "\n[Testing MINUTELY AGGREGATED data]";

        // fetch the datetime of the last run of the aggregation job
        $res = $this->run_db_query("SELECT start from statistics_cron_runtime WHERE type='minutely'");
        $row = $res->fetch_assoc();
        $previous_start_datetime = $row['start'];

        for ($i = 0; $i < self::NUM_TEST_RUNS; $i++) {
            $filters = $this->generate_test_filters();
            $date_filter = sprintf("%s-%s-%s %s:%s:00", $filters['year'], $filters['month'], $filters['day'], $filters['hour'], $filters['minute']);
            $metric_column = $this->metric_index[$filters['metric']];

            echo "\nDoing test run #" . ($i + 1) . " with filters ";
            echo sprintf("(metric: %d, datetime: %s) ... ", $filters['metric'], $date_filter);

            $sql_preaggregated_count = sprintf("select count(*) as cnt from statistics where metric=%d and year=%d and month=%d and day=%d and hour=%d and minute=%d and type != 0",
                $filters['metric'], $filters['year'], $filters['month'], $filters['day'], $filters['hour'], $filters['minute']);
            $res = $this->run_db_query($sql_preaggregated_count);
            $row = $res->fetch_assoc();
            $preaggregated_count = $row['cnt'];

            $sql_real_data = sprintf("select %d as metric, a.type,YEAR(b.datetime) AS year, MONTH(b.datetime) AS month, DAYOFMONTH(b.datetime) AS day, HOUR(b.datetime) AS hour, MINUTE(b.datetime) AS minute, cast(%s as char(64)) AS metric_id, count(*) as value from transactions AS a, activity AS b  WHERE a.activity_id = b.id AND a.activity_id IS NOT NULL AND b.datetime like '%d-%02s-%02s %02s:%02s%%' AND a.datetime < DATE_FORMAT('%s', '%%Y-%%m-%%d %%H:%%i:00') GROUP BY `metric`,`type`,`year`,`month`,`day`,`hour`,`minute`,`metric_id`",
                $filters['metric'], $metric_column, $filters['year'], $filters['month'], $filters['day'], $filters['hour'], $filters['minute'], $previous_start_datetime);

            $sql_preaggregated = sprintf("select metric, type, year, month, day, hour, minute, metric_id, value from statistics where metric=%d and year=%d and month=%d and day=%d and hour=%d and minute=%d and type != 0",
                $filters['metric'], $filters['year'], $filters['month'], $filters['day'], $filters['hour'], $filters['minute']);

            $sql = "SELECT COUNT(*) AS cnt FROM ($sql_preaggregated) AS tbl1 RIGHT JOIN ($sql_real_data) AS tbl2 USING (metric, type, year, month, day, hour, minute, metric_id, value) WHERE tbl1.metric is NOT NULL";
            $res = $this->run_db_query($sql);
            $row = $res->fetch_assoc();
            $preagg_realdata_matched_count = $row['cnt'];

            // Check if the number of records for the particular filters
            // from the preaggregated statistics table is equal to the number
            // of matched records
            if ($preaggregated_count == $preagg_realdata_matched_count)
                echo "\nOK";
            else {
                echo "\nFAILED";
                $this->test_failures++;
            }
        }

        echo "\n";
    }

    private function test_hourly_data() {
        echo "\n[Testing HOURLY AGGREGATED data]";

        // fetch the datetime of the last run of the aggregation job
        $res = $this->run_db_query("SELECT start from statistics_cron_runtime WHERE type='hourly'");
        $row = $res->fetch_assoc();
        $previous_start_datetime = $row['start'];

        for ($i = 0; $i < self::NUM_TEST_RUNS; $i++) {
            $filters = $this->generate_test_filters();
            $date_filter = sprintf("%s-%s-%s %s:00:00", $filters['year'], $filters['month'], $filters['day'], $filters['hour']);
            $metric_column = $this->metric_index[$filters['metric']];

            echo "\nDoing test run #" . ($i + 1) . " with filters ";
            echo sprintf("(metric: %d, datetime: %s) ... ", $filters['metric'], $date_filter);

            $sql_preaggregated_count = sprintf("select count(*) as cnt from statistics_hourly where metric=%d and year=%d and month=%d and day=%d and hour=%d and type != 0",
                $filters['metric'], $filters['year'], $filters['month'], $filters['day'], $filters['hour']);
            $res = $this->run_db_query($sql_preaggregated_count);
            $row = $res->fetch_assoc();
            $preaggregated_count = $row['cnt'];

            $sql_real_data = sprintf("select %d as metric, a.type,YEAR(b.datetime) AS year, MONTH(b.datetime) AS month, DAYOFMONTH(b.datetime) AS day, HOUR(b.datetime) AS hour, cast(%s as char(64)) AS metric_id, count(*) as value from transactions AS a, activity AS b  WHERE a.activity_id = b.id AND a.activity_id IS NOT NULL AND b.datetime like '%d-%02s-%02s %02s%%' AND a.datetime < DATE_FORMAT('%s', '%%Y-%%m-%%d %%H:00:00') GROUP BY `metric`,`type`,`year`,`month`,`day`,`hour`,`metric_id`",
                $filters['metric'], $metric_column, $filters['year'], $filters['month'], $filters['day'], $filters['hour'], $previous_start_datetime);

            $sql_preaggregated = sprintf("select metric, type, year, month, day, hour, metric_id, value from statistics_hourly where metric=%d and year=%d and month=%d and day=%d and hour=%d and type != 0",
                $filters['metric'], $filters['year'], $filters['month'], $filters['day'], $filters['hour']);

            $sql = "SELECT COUNT(*) AS cnt FROM ($sql_preaggregated) AS tbl1 RIGHT JOIN ($sql_real_data) AS tbl2 USING (metric, type, year, month, day, hour, metric_id, value) WHERE tbl1.metric is NOT NULL";
            $res = $this->run_db_query($sql);
            $row = $res->fetch_assoc();
            $preagg_realdata_matched_count = $row['cnt'];

            // Check if the number of records for the particular filters
            // from the preaggregated statistics table is equal to the number
            // of matched records
            if ($preaggregated_count == $preagg_realdata_matched_count)
                echo "\nOK";
            else {
                echo "\nFAILED";
                $this->test_failures++;
            }
        }

        echo "\n";
    }

    private function test_daily_data() {
        echo "\n[Testing DAILY AGGREGATED data]";

        // fetch the datetime of the last run of the aggregation job
        $res = $this->run_db_query("SELECT start from statistics_cron_runtime WHERE type='daily'");
        $row = $res->fetch_assoc();
        $previous_start_datetime = $row['start'];

        for ($i = 0; $i < self::NUM_TEST_RUNS; $i++) {
            $filters = $this->generate_test_filters();
            $date_filter = sprintf("%s-%s-%s 00:00:00", $filters['year'], $filters['month'], $filters['day']);
            $metric_column = $this->metric_index[$filters['metric']];

            echo "\nDoing test run #" . ($i + 1) . " with filters ";
            echo sprintf("(metric: %d, datetime: %s) ... ", $filters['metric'], $date_filter);

            $sql_preaggregated_count = sprintf("select count(*) as cnt from statistics_daily where metric=%d and year=%d and month=%d and day=%d and type != 0",
                $filters['metric'], $filters['year'], $filters['month'], $filters['day']);
            $res = $this->run_db_query($sql_preaggregated_count);
            $row = $res->fetch_assoc();
            $preaggregated_count = $row['cnt'];

            $sql_real_data = sprintf("select %d as metric, a.type,YEAR(b.datetime) AS year, MONTH(b.datetime) AS month, DAYOFMONTH(b.datetime) AS day, cast(%s as char(64)) AS metric_id, count(*) as value from transactions AS a, activity AS b  WHERE a.activity_id = b.id AND a.activity_id IS NOT NULL AND b.datetime like '%d-%02s-%02s%%' AND a.datetime < DATE_FORMAT('%s', '%%Y-%%m-%%d 00:00:00') GROUP BY `metric`,`type`,`year`,`month`,`day`,`metric_id`",
                $filters['metric'], $metric_column, $filters['year'], $filters['month'], $filters['day'], $previous_start_datetime);

            $sql_preaggregated = sprintf("select metric, type, year, month, day, metric_id, value from statistics_daily where metric=%d and year=%d and month=%d and day=%d and type != 0",
                $filters['metric'], $filters['year'], $filters['month'], $filters['day']);

            $sql = "SELECT COUNT(*) AS cnt FROM ($sql_preaggregated) AS tbl1 RIGHT JOIN ($sql_real_data) AS tbl2 USING (metric, type, year, month, day, metric_id, value) WHERE tbl1.metric is NOT NULL";
            $res = $this->run_db_query($sql);
            $row = $res->fetch_assoc();
            $preagg_realdata_matched_count = $row['cnt'];

            // Check if the number of records for the particular filters
            // from the preaggregated statistics table is equal to the number
            // of matched records
            if ($preaggregated_count == $preagg_realdata_matched_count)
                echo "\nOK";
            else {
                echo "\nFAILED";
                $this->test_failures++;
            }
        }

        echo "\n";
    }

    private function test_monthly_data() {
        echo "\n[Testing MONTHLY AGGREGATED data]";

        // fetch the datetime of the last run of the aggregation job
        $res = $this->run_db_query("SELECT start from statistics_cron_runtime WHERE type='monthly'");
        $row = $res->fetch_assoc();
        $previous_start_datetime = $row['start'];

        for ($i = 0; $i < self::NUM_TEST_RUNS; $i++) {
            $filters = $this->generate_test_filters();
            $date_filter = sprintf("%s-%s-00 00:00:00", $filters['year'], $filters['month']);
            $metric_column = $this->metric_index[$filters['metric']];

            echo "\nDoing test run #" . ($i + 1) . " with filters ";
            echo sprintf("(metric: %d, datetime: %s) ... ", $filters['metric'], $date_filter);

            $sql_preaggregated_count = sprintf("select count(*) as cnt from statistics_monthly where metric=%d and year=%d and month=%d and type != 0",
                $filters['metric'], $filters['year'], $filters['month']);
            $res = $this->run_db_query($sql_preaggregated_count);
            $row = $res->fetch_assoc();
            $preaggregated_count = $row['cnt'];

            $sql_real_data = sprintf("select %d as metric, a.type,YEAR(b.datetime) AS year, MONTH(b.datetime) AS month, cast(%s as char(64)) AS metric_id, count(*) as value from transactions AS a, activity AS b  WHERE a.activity_id = b.id AND a.activity_id IS NOT NULL AND b.datetime like '%d-%02s-%%' AND a.datetime < '%s' GROUP BY `metric`,`type`,`year`,`month`,`metric_id`",
                $filters['metric'], $metric_column, $filters['year'], $filters['month'], $previous_start_datetime);

            $sql_preaggregated = sprintf("select metric, type, year, month, metric_id, value from statistics_monthly where metric=%d and year=%d and month=%d and type != 0",
                $filters['metric'], $filters['year'], $filters['month']);

            $sql = "SELECT COUNT(*) AS cnt FROM ($sql_preaggregated) AS tbl1 RIGHT JOIN ($sql_real_data) AS tbl2 USING (metric, type, year, month, metric_id, value) WHERE tbl1.metric is NOT NULL";
            $res = $this->run_db_query($sql);
            $row = $res->fetch_assoc();
            $preagg_realdata_matched_count = $row['cnt'];

            // Check if the number of records for the particular filters
            // from the preaggregated statistics table is equal to the number
            // of matched records
            if ($preaggregated_count == $preagg_realdata_matched_count)
                echo "\nOK";
            else {
                echo "\nFAILED";
                $this->test_failures++;
            }
        }

        echo "\n";
    }
}

// This is where we run the tests
try {
    $stats_test = new Stats_aggregation_test();
    $stats_test->run_tests();
} catch (Exception $e) {
    echo "\nException: \n", $e->getMessage(), "\n";

    // send an email notifying about the failure
    $subject = 'Statistics aggregation tests failed';
    $stats_test->send_email(Config::$error_email_to, Config::$error_email_from, $subject, $e->getMessage());
}

?>
