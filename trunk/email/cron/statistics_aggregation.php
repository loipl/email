#!/bin/env php
<?php

include_once dirname(__FILE__) . '/../Config.php';
include_once 'statistics.php';

date_default_timezone_set(Config::$db_timezone);

class Stats_aggregator extends Stats {
    const AGGREGATION_TYPE_MINUTELY = 'minutely';
    const AGGREGATION_TYPE_HOURLY = 'hourly';
    const AGGREGATION_TYPE_DAILY = 'daily';
    const AGGREGATION_TYPE_MONTHLY = 'monthly';

    private $available_aggregation_types = array(
        self::AGGREGATION_TYPE_MINUTELY,
        self::AGGREGATION_TYPE_HOURLY,
        self::AGGREGATION_TYPE_DAILY,
        self::AGGREGATION_TYPE_MONTHLY);

    private $aggregation_type;

    public function __construct($aggregation_type=self::AGGREGATION_TYPE_MINUTELY, $log_level) {
        $this->aggregation_type = $aggregation_type;
        $this->log_level = $log_level;

        $this->validate_aggregation_type();

        $this->log_message("Starting " . $this->aggregation_type . " aggregation", "debug");
        $this->connect_db();
    }

    public function do_aggregation() {
        switch($this->aggregation_type) {
            case self::AGGREGATION_TYPE_MINUTELY:
                $this->do_aggregation_low_level(self::AGGREGATION_TYPE_MINUTELY);
                break;

            case self::AGGREGATION_TYPE_HOURLY:
                $this->do_aggregation_low_level(self::AGGREGATION_TYPE_HOURLY);
                break;

            case self::AGGREGATION_TYPE_DAILY:
                $this->do_aggregation_low_level(self::AGGREGATION_TYPE_DAILY);
                break;

            case self::AGGREGATION_TYPE_MONTHLY:
                $this->do_aggregation_low_level(self::AGGREGATION_TYPE_MONTHLY);
                break;
        }
    }

    private function validate_aggregation_type() {
        if (!in_array($this->aggregation_type, $this->available_aggregation_types)) {
            $msg = "Incorrect aggregation type (" . $this->aggregation_type . ")";
            throw new Exception($msg);
        }

        return true;
    }

    private function do_aggregation_low_level($type=self::AGGREGATION_TYPE_MINUTELY) {
        // Entire aggregation process should be done inside a transation so that we do not have half baked statistics
        if(!$this->run_db_query('START TRANSACTION'))
            return false;

        // Fetch the last run time because we will fetch the results 
        $res = $this->run_db_query("SELECT start FROM statistics_cron_runtime WHERE type='$type' FOR UPDATE");
        if(!$res)
            return $this->rollback_trx();

        $row = $res->fetch_assoc();
        $previous_start_datetime = $row['start'];

        if($this->has_aggregation_done($type, $previous_start_datetime)) {
            $msg = "Aggregation [$type] has already been run once for the current time period";
            throw new Exception($msg);
        }

        if (!$this->run_db_query("UPDATE statistics_cron_runtime SET start=now() WHERE type='$type'"))
            return $this->rollback_trx();

        // generate and execute aggregation queries
        foreach ($this->metric_index as $key => $value) {
            switch ($type) {
                case self::AGGREGATION_TYPE_MINUTELY:
                    // minutely aggregation query
                    if (!$this->run_minutely_aggregation_query($key, $previous_start_datetime))
                        return $this->rollback_trx();
                    break;

                case self::AGGREGATION_TYPE_HOURLY:
                    // hourly aggregation query
                    if (!$this->run_hourly_aggregation_query($key, $previous_start_datetime))
                        return $this->rollback_trx();

                    break;


                case self::AGGREGATION_TYPE_DAILY:
                    // daily aggregation query
                    if (!$this->run_daily_aggregation_query($key, $previous_start_datetime))
                        return $this->rollback_trx();

                    break;

                case self::AGGREGATION_TYPE_MONTHLY:
                    // monthly aggregation query
                    if (!$this->run_monthly_aggregation_query($key, $previous_start_datetime))
                        return $this->rollback_trx();

                    break;
            }
        }

        // update the datetime field to reflect when the aggregation ended
        if (!$this->run_db_query("UPDATE statistics_cron_runtime SET end=now() WHERE type='$type'"))
            return $this->rollback_trx();

        if(!$this->run_db_query("COMMIT"))
            return false;

        $this->log_message("Completed " . $type . " aggregation\n", "debug");

        return true;
    }

    /* 
        Check to see if aggregation has already been done for:
        a. this minute if this is a minutely aggregation run
        b. this hour if this is a hourly aggregation run
    */
    private function has_aggregation_done($type=self::AGGREGATION_TYPE_MINUTELY, $previous_start_datetime) {
        $start_datetime = new DateTime($previous_start_datetime);
        $current_datetime = new DateTime('NOW');

        switch ($type) {
            case self::AGGREGATION_TYPE_MINUTELY:
                $datetime_format='Y-m-d H:i:00';
                break;

            case self::AGGREGATION_TYPE_HOURLY:
                $datetime_format='Y-m-d H:00:00';
                break;

            case self::AGGREGATION_TYPE_DAILY:
                $datetime_format='Y-m-d 00:00:00';
                break;

            case self::AGGREGATION_TYPE_MONTHLY:
                $datetime_format='Y-m-01 00:00:00';
                break;
        }

        if($start_datetime->format($datetime_format) == $current_datetime->format($datetime_format)) 
            return true;

        return false;
    }

    private function run_minutely_aggregation_query($metric, $previous_start_datetime) {
        if(!array_key_exists($metric, $this->metric_index))
            return false;

        $table_column = $this->metric_index[$metric];
        $sql = sprintf("INSERT INTO statistics(`metric`, `type`, `year`, `month`, `day`, `hour`, `minute`, `metric_id`, `value`, `datetime`) SELECT %d AS metric, a.type, YEAR(b.datetime) AS year, MONTH(b.datetime) AS month, DAYOFMONTH(b.datetime) AS day, HOUR(b.datetime) AS hour, MINUTE(b.datetime) AS minute, %s AS metric_id, COUNT(*) AS value, CONCAT(YEAR(b.datetime), '-', MONTH(b.datetime), '-', DAYOFMONTH(b.datetime), ' ', HOUR(b.datetime), ':', MINUTE(b.datetime), ':00') AS dt FROM transactions AS a, activity AS b WHERE a.activity_id = b.id AND a.activity_id IS NOT NULL AND a.datetime >= DATE_FORMAT('%s', '%%Y-%%m-%%d %%H:%%i:00') AND a.datetime < DATE_FORMAT(NOW(), '%%Y-%%m-%%d %%H:%%i:00') GROUP BY `metric`,`type`,`year`,`month`,`day`,`hour`,`minute`,`metric_id` ON DUPLICATE KEY UPDATE `value`=`value`+VALUES(`value`)", $metric, $table_column, $previous_start_datetime);
        if (!$this->run_db_query($sql))
            return false;

        $table_column = $this->metric_index_2[$metric];
        $sql = sprintf("INSERT INTO statistics(`metric`, `type`, `year`, `month`, `day`, `hour`, `minute`, `metric_id`, `value`, `datetime`) SELECT %d AS metric, 0 AS type, YEAR(a.datetime) AS year, MONTH(a.datetime) AS month, DAYOFMONTH(a.datetime) AS day, HOUR(a.datetime) AS hour, MINUTE(a.datetime) AS minute, %s AS metric_id, COUNT(*) AS value, CONCAT(YEAR(a.datetime), '-', MONTH(a.datetime), '-', DAYOFMONTH(a.datetime), ' ', HOUR(a.datetime), ':', MINUTE(a.datetime), ':00') AS dt FROM activity AS a WHERE a.datetime >= DATE_FORMAT('%s', '%%Y-%%m-%%d %%H:%%i:00') AND a.datetime < DATE_FORMAT(NOW(), '%%Y-%%m-%%d %%H:%%i:00') GROUP BY `metric`,`type`,`year`,`month`,`day`,`hour`,`minute`,`metric_id` ON DUPLICATE KEY UPDATE `value`=`value`+VALUES(`value`)", $metric, $table_column, $previous_start_datetime);
        if (!$this->run_db_query($sql))
            return false;

        return true;
    }

    private function run_hourly_aggregation_query($metric, $previous_start_datetime) {
        if(!array_key_exists($metric, $this->metric_index))
            return false;

        $table_column = $this->metric_index[$metric];
        $sql = sprintf("INSERT INTO statistics_hourly(`metric`, `type`, `year`, `month`, `day`, `hour`, `metric_id`, `value`, `datetime`) SELECT %d AS metric, a.type, YEAR(b.datetime) AS year, MONTH(b.datetime) AS month, DAYOFMONTH(b.datetime) AS day, HOUR(b.datetime) AS hour, %s AS metric_id, COUNT(*) AS value, CONCAT(YEAR(b.datetime), '-', MONTH(b.datetime), '-', DAYOFMONTH(b.datetime), ' ', HOUR(b.datetime), ':00:00') AS dt FROM transactions AS a, activity AS b WHERE a.activity_id = b.id AND a.activity_id IS NOT NULL AND a.datetime >= DATE_FORMAT('%s', '%%Y-%%m-%%d %%H:00:00') AND a.datetime < DATE_FORMAT(NOW(), '%%Y-%%m-%%d %%H:00:00') GROUP BY `metric`,`type`,`year`,`month`,`day`,`hour`,`metric_id` ON DUPLICATE KEY UPDATE `value`=`value`+VALUES(`value`)", $metric, $table_column, $previous_start_datetime);
        if (!$this->run_db_query($sql))
            return false;

        $table_column = $this->metric_index_2[$metric];
        $sql = sprintf("INSERT INTO statistics_hourly(`metric`, `type`, `year`, `month`, `day`, `hour`, `metric_id`, `value`, `datetime`) SELECT %d AS metric, 0 AS type, YEAR(a.datetime) AS year, MONTH(a.datetime) AS month, DAYOFMONTH(a.datetime) AS day, HOUR(a.datetime) AS hour, %s AS metric_id, COUNT(*) AS value, CONCAT(YEAR(a.datetime), '-', MONTH(a.datetime), '-', DAYOFMONTH(a.datetime), ' ', HOUR(a.datetime), ':00:00') AS dt FROM activity AS a WHERE a.datetime >= DATE_FORMAT('%s', '%%Y-%%m-%%d %%H:00:00') AND a.datetime < DATE_FORMAT(NOW(), '%%Y-%%m-%%d %%H:00:00') GROUP BY `metric`,`type`,`year`,`month`,`day`,`hour`,`metric_id` ON DUPLICATE KEY UPDATE `value`=`value`+VALUES(`value`)", $metric, $table_column, $previous_start_datetime);
        if (!$this->run_db_query($sql))
            return false;

        return true;
    }

    private function run_daily_aggregation_query($metric, $previous_start_datetime) {
        if(!array_key_exists($metric, $this->metric_index))
            return false;

        $table_column = $this->metric_index[$metric];
        $sql = sprintf("INSERT INTO statistics_daily(`metric`, `type`, `year`, `month`, `day`, `metric_id`, `value`, `datetime`) SELECT %d AS metric, a.type, YEAR(b.datetime) AS year, MONTH(b.datetime) AS month, DAYOFMONTH(b.datetime) AS day, %s AS metric_id, COUNT(*) AS value, CONCAT(YEAR(b.datetime), '-', MONTH(b.datetime), '-', DAYOFMONTH(b.datetime), ' 00:00:00') AS dt FROM transactions AS a, activity AS b WHERE a.activity_id = b.id AND a.activity_id IS NOT NULL AND a.datetime >= DATE_FORMAT('%s', '%%Y-%%m-%%d 00:00:00') AND a.datetime < DATE_FORMAT(NOW(), '%%Y-%%m-%%d 00:00:00') GROUP BY `metric`,`type`,`year`,`month`,`day`,`metric_id` ON DUPLICATE KEY UPDATE `value`=`value`+VALUES(`value`)", $metric, $table_column, $previous_start_datetime);
        if (!$this->run_db_query($sql))
            return false;

        $table_column = $this->metric_index_2[$metric];
        $sql = sprintf("INSERT INTO statistics_daily(`metric`, `type`, `year`, `month`, `day`, `metric_id`, `value`, `datetime`) SELECT %d AS metric, 0 AS type, YEAR(a.datetime) AS year, MONTH(a.datetime) AS month, DAYOFMONTH(a.datetime) AS day, %s AS metric_id, COUNT(*) AS value, CONCAT(YEAR(a.datetime), '-', MONTH(a.datetime), '-', DAYOFMONTH(a.datetime), ' 00:00:00') AS dt FROM activity AS a WHERE a.datetime >= DATE_FORMAT('%s', '%%Y-%%m-%%d 00:00:00') AND a.datetime < DATE_FORMAT(NOW(), '%%Y-%%m-%%d 00:00:00') GROUP BY `metric`,`type`,`year`,`month`,`day`,`metric_id` ON DUPLICATE KEY UPDATE `value`=`value`+VALUES(`value`)", $metric, $table_column, $previous_start_datetime);
        if (!$this->run_db_query($sql))
            return false;

        return true;
    }

    private function run_monthly_aggregation_query($metric, $previous_start_datetime) {
        if(!array_key_exists($metric, $this->metric_index))
            return false;

        $table_column = $this->metric_index[$metric];
        $sql = sprintf("INSERT INTO statistics_monthly(`metric`, `type`, `year`, `month`, `metric_id`, `value`, `datetime`) SELECT %d AS metric, a.type, YEAR(b.datetime) AS year, MONTH(b.datetime) AS month, %s AS metric_id, COUNT(*) AS value, CONCAT(YEAR(b.datetime), '-', MONTH(b.datetime), '-00 00:00:00') AS dt FROM transactions AS a, activity AS b WHERE a.activity_id = b.id AND a.activity_id IS NOT NULL AND a.datetime >= DATE_FORMAT('%s', '%%Y-%%m-01 00:00:00') AND a.datetime < DATE_FORMAT(NOW(), '%%Y-%%m-01 00:00:00') GROUP BY `metric`,`type`,`year`,`month`,`metric_id` ON DUPLICATE KEY UPDATE `value`=`value`+VALUES(`value`)", $metric, $table_column, $previous_start_datetime);
        if (!$this->run_db_query($sql))
            return false;

        $table_column = $this->metric_index_2[$metric];
        $sql = sprintf("INSERT INTO statistics_monthly(`metric`, `type`, `year`, `month`, `metric_id`, `value`, `datetime`) SELECT %d AS metric, 0 AS type, YEAR(a.datetime) AS year, MONTH(a.datetime) AS month, %s AS metric_id, COUNT(*) AS value, CONCAT(YEAR(a.datetime), '-', MONTH(a.datetime), '-00 00:00:00') AS dt FROM activity AS a WHERE a.datetime >= DATE_FORMAT('%s', '%%Y-%%m-01 00:00:00') AND a.datetime < DATE_FORMAT(NOW(), '%%Y-%%m-01 00:00:00') GROUP BY `metric`,`type`,`year`,`month`,`metric_id` ON DUPLICATE KEY UPDATE `value`=`value`+VALUES(`value`)", $metric, $table_column, $previous_start_datetime);
        if (!$this->run_db_query($sql))
            return false;

        return true;
    }
}

// option parsing
$shortopts  = "";
$shortopts .= "t:"; // the aggregation type (minutely, hourly, daily, monthly)

$longopts = array(
    "type:" // the aggregation type (minutely, hourly, daily, monthly)
);

$options = getopt($shortopts, $longopts);

// PID related stuff to make sure that two instances of the 
// same script are not running at the same time
$pid_filename = basename(Config::$pid_file, '.pid') . '-' . $options['type'] . '.pid';
if (file_exists($pid_filename)) {
    $running_pid = Stats::read_pid_from_file($pid_filename);

    if (Stats::is_pid_running($running_pid)) {
        // A stats aggregation run is currently running
        // so we should not do another run at the same time
        exit(2);
    } else {
        // this is a stale pid file so should be deleted
        unlink($pid_filename);
    }
}

// We are here meaning there is no stats aggregation process currently running
$current_pid = getmypid();
file_put_contents($pid_filename, $current_pid);

// this is where we do the actual work
try {
    $aggregator = new Stats_aggregator($options['type'], Config::$log_level);
    $aggregator->do_aggregation();
} catch (Exception $e) {
    echo "\nException: \n", $e->getMessage(), "\n";

    // send an email notifying about the failure
    $subject = 'Statistics aggregation ' . $options['type'] . ' failed';
    $aggregator->send_email(Config::$error_email_to, Config::$error_email_from, $subject, $e->getMessage());

    unlink($pid_filename);
    exit(1);
}

// return a successful exit code
unlink($pid_filename);
exit(0);

?>
