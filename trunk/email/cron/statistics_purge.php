#!/bin/env php
<?php

include_once dirname(__FILE__) . '/../Config.php';
include_once 'statistics.php';

date_default_timezone_set(Config::$db_timezone);

class Stats_purge extends Stats {
    const AGGREGATION_TYPE_MINUTELY = 'minutely';
    const AGGREGATION_TYPE_HOURLY = 'hourly';
    const AGGREGATION_TYPE_DAILY = 'daily';
    const AGGREGATION_TYPE_MONTHLY = 'monthly';

    private $available_aggregation_types = array(
        self::AGGREGATION_TYPE_MINUTELY,
        self::AGGREGATION_TYPE_HOURLY,
        self::AGGREGATION_TYPE_DAILY,
        self::AGGREGATION_TYPE_MONTHLY);

    public function __construct($log_level) {
        $this->log_level = $log_level;

        $this->log_message("Purging aged aggregation data", "debug");
        $this->connect_db();
    }

    public function do_purge() {
        // Entire purge process should be done inside a transation so that we do not have half baked statistics
        if(!$this->run_db_query('START TRANSACTION'))
            return false;

        $current_datetime = new DateTime('NOW');

        foreach ($this->available_aggregation_types as $type) {
            switch ($type) {
                case self::AGGREGATION_TYPE_MINUTELY:
                    $diff = new DateInterval(Config::$age_minutely_stats);
                    $table_name = 'statistics';
                    break;

                case self::AGGREGATION_TYPE_HOURLY:
                    $diff = new DateInterval(Config::$age_hourly_stats);
                    $table_name = 'statistics_hourly';
                    break;

                case self::AGGREGATION_TYPE_DAILY:
                    $diff = new DateInterval(Config::$age_daily_stats);
                    $table_name = 'statistics_daily';
                    break;

                case self::AGGREGATION_TYPE_MONTHLY:
                    $diff = new DateInterval(Config::$age_monthly_stats);
                    $table_name = 'statistics_monthly';
                    break;
            }

            // calculate the date from which everything earlier will be purged
            $current_datetime->sub($diff);
            $purge_threshold_datetime = $current_datetime->format('Y-m-d H:i:s');

            $this->log_message("Purging records from $table_name older than $purge_threshold_datetime", "debug");

            $sql = "DELETE FROM $table_name WHERE datetime < '$purge_threshold_datetime'";
            if(!$this->run_db_query($sql))
                return false;
        }

        if(!$this->run_db_query("COMMIT"))
            return false;

        $this->log_message("Completed purging aged aggregation data", "debug");
        return true;
    }
}

// this is where we do the actual work
try {
    $purger = new Stats_purge(Config::$log_level);
    $purger->do_purge();
} catch (Exception $e) {
    echo "\nException: \n", $e->getMessage(), "\n";
}

?>
