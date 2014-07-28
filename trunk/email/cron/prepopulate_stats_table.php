#!/bin/env php
<?php

include_once dirname(__FILE__) . '/../Config.php';
include_once 'statistics.php';

date_default_timezone_set(Config::$db_timezone);

class Stats_prepopulate extends Stats {
    private $statistics_tables = array('statistics', 'statistics_hourly', 'statistics_daily', 'statistics_monthly', 'statistics_cron_runtime');

    public function __construct($log_level) {
        $this->log_level = $log_level;

        $this->connect_db();
    }

    public function do_cleanup() {
        foreach ($this->statistics_tables as $table) {
            $this->run_db_query("TRUNCATE TABLE `$table`");
        }
    }

    public function do_populate() {
        // We will run aggregation on data worth 1 day at a time
        $diff1Day = new DateInterval('P1D');

        // We need to have aggregated data starting from Jan 1, 2014
        $chunk_datetime_start = new DateTime('2014-01-01 00:00:00');

        // We need to aggregate data up to yesterday
        $current_datetime = new DateTime('NOW');
        $current_datetime->sub($diff1Day);

        // Entire aggregation process should be done inside a transation so that we do not have half baked statistics
        if(!$this->run_db_query('START TRANSACTION'))
            return false;

        // Setup the table that stores the statistics cron run time
        if(!$this->init_cron_runtime_table($current_datetime->format('Y-m-d H:i:s')))
            return false;

        // Keep running aggregation until we have not aggregated
        // all the data up to yesterday
        while (true) {
            $chunk_datetime_end = new DateTime($chunk_datetime_start->format('Y-m-d H:i:s'));
            $chunk_datetime_end->add($diff1Day);

            if ($chunk_datetime_end >= $current_datetime)
                break;

            // generate and execute aggregation queries
            foreach ($this->metric_index as $key => $value) {
                $chunk_datetime_start_str = $chunk_datetime_start->format('Y-m-d H:i:s');
                $chunk_datetime_end_str = $chunk_datetime_end->format('Y-m-d H:i:s');

                if (!$this->run_minutely_aggregation_query($key, $chunk_datetime_start_str, $chunk_datetime_end_str))
                    return $this->rollback_trx();

                if (!$this->run_hourly_aggregation_query($key, $chunk_datetime_start_str, $chunk_datetime_end_str))
                    return $this->rollback_trx();

                if (!$this->run_daily_aggregation_query($key, $chunk_datetime_start_str, $chunk_datetime_end_str))
                    return $this->rollback_trx();

                if (!$this->run_monthly_aggregation_query($key, $chunk_datetime_start_str, $chunk_datetime_end_str))
                    return $this->rollback_trx();
            }

            // We reset start datetime to the end datetime
            // so that we can process the next chunk of data
            $chunk_datetime_start = $chunk_datetime_end;
        }

        // Update the table that stores the statistics cron run time
        if(!$this->update_cron_runtime_table($current_datetime->format('Y-m-d H:i:s')))
            return false;

        if(!$this->run_db_query("COMMIT"))
            return false;

        return true;
    }

    private function run_minutely_aggregation_query($metric, $start_datetime, $end_datetime) {
        if(!array_key_exists($metric, $this->metric_index))
            return false;

        $table_column = $this->metric_index[$metric];

        $sql = sprintf("INSERT INTO statistics(`metric`, `type`, `year`, `month`, `day`, `hour`, `minute`, `metric_id`, `value`, `datetime`) SELECT %d AS metric, a.type, YEAR(b.datetime) AS year, MONTH(b.datetime) AS month, DAYOFMONTH(b.datetime) AS day, HOUR(b.datetime) AS hour,  MINUTE(b.datetime) AS minute, %s AS metric_id, COUNT(*) AS value, CONCAT(YEAR(b.datetime), '-', MONTH(b.datetime), '-', DAYOFMONTH(b.datetime), ' ', HOUR(b.datetime), ':', MINUTE(b.datetime), ':00') AS dt FROM transactions AS a, activity AS b WHERE a.activity_id = b.id AND a.activity_id IS NOT NULL AND a.datetime >= '%s' AND a.datetime < '%s' GROUP BY `metric`,`type`,`year`,`month`,`day`,`hour`,`minute`,`metric_id` ON DUPLICATE KEY UPDATE `value`=`value`+VALUES(`value`)", $metric, $table_column, $start_datetime, $end_datetime);
        if (!$this->run_db_query($sql))
            return false;

        $table_column = $this->metric_index_2[$metric];
        $sql = sprintf("INSERT INTO statistics(`metric`, `type`, `year`, `month`, `day`, `hour`, `minute`, `metric_id`, `value`, `datetime`) SELECT %d AS metric, 0 AS type, YEAR(a.datetime) AS year, MONTH(a.datetime) AS month, DAYOFMONTH(a.datetime) AS day, HOUR(a.datetime) AS hour,  MINUTE(a.datetime) AS minute, %s AS metric_id, COUNT(*) AS value, CONCAT(YEAR(a.datetime), '-', MONTH(a.datetime), '-', DAYOFMONTH(a.datetime), ' ', HOUR(a.datetime), ':', MINUTE(a.datetime), ':00') AS dt FROM activity AS a WHERE a.datetime >= '%s' AND a.datetime < '%s' GROUP BY `metric`,`type`,`year`,`month`,`day`,`hour`,`minute`,`metric_id` ON DUPLICATE KEY UPDATE `value`=`value`+VALUES(`value`)", $metric, $table_column, $start_datetime,$end_datetime);
        if (!$this->run_db_query($sql))
            return false;

        return true;
    }

    private function run_hourly_aggregation_query($metric, $start_datetime, $end_datetime) {
        if(!array_key_exists($metric, $this->metric_index))
            return false;

        $table_column = $this->metric_index[$metric];

        $sql = sprintf("INSERT INTO statistics_hourly(`metric`, `type`, `year`, `month`, `day`, `hour`, `metric_id`, `value`, `datetime`) SELECT %d AS metric, a.type, YEAR(b.datetime) AS year, MONTH(b.datetime) AS month, DAYOFMONTH(b.datetime) AS day, HOUR(b.datetime) AS hour, %s AS  metric_id, COUNT(*) AS value, CONCAT(YEAR(b.datetime), '-', MONTH(b.datetime), '-', DAYOFMONTH(b.datetime), ' ', HOUR(b.datetime), ':00:00') AS dt FROM transactions AS a, activity AS b WHERE a.activity_id = b.id AND a.activity_id IS NOT NULL AND a.datetime >= '%s' AND a.datetime < '%s' GROUP BY `metric`,`type`,`year`,`month`,`day`,`hour`,`metric_id` ON DUPLICATE KEY UPDATE `value`=`value`+VALUES(`value`)", $metric, $table_column, $start_datetime, $end_datetime);
        if (!$this->run_db_query($sql))
            return false;

        $table_column = $this->metric_index_2[$metric];
        $sql = sprintf("INSERT INTO statistics_hourly(`metric`, `type`, `year`, `month`, `day`, `hour`, `metric_id`, `value`, `datetime`) SELECT %d AS metric, 0 AS type, YEAR(a.datetime) AS year, MONTH(a.datetime) AS month, DAYOFMONTH(a.datetime) AS day, HOUR(a.datetime) AS hour, %s  AS metric_id, COUNT(*) AS value, CONCAT(YEAR(a.datetime), '-', MONTH(a.datetime), '-', DAYOFMONTH(a.datetime), ' ', HOUR(a.datetime), ':00:00') AS dt FROM activity AS a WHERE a.datetime >= '%s' AND a.datetime < '%s' GROUP BY `metric`,`type`,`year`,`month`,`day`,`hour`,`metric_id` ON DUPLICATE KEY UPDATE `value`=`value`+VALUES(`value`)", $metric, $table_column, $start_datetime, $end_datetime);
        if (!$this->run_db_query($sql))
            return false;

        return true;
    }

    private function run_daily_aggregation_query($metric, $start_datetime, $end_datetime) {
        if(!array_key_exists($metric, $this->metric_index))
            return false;

        $table_column = $this->metric_index[$metric];

        $sql = sprintf("INSERT INTO statistics_daily(`metric`, `type`, `year`, `month`, `day`, `metric_id`, `value`, `datetime`) SELECT %d AS metric, a.type, YEAR(b.datetime) AS year, MONTH(b.datetime) AS month, DAYOFMONTH(b.datetime) AS day, %s AS metric_id, COUNT(*) AS value, CONCAT(YEAR(b.datetime), '-', MONTH(b.datetime), '-', DAYOFMONTH(b.datetime), ' 00:00:00') AS dt FROM transactions AS a, activity AS b WHERE a.activity_id = b.id AND a.activity_id IS NOT NULL AND a.datetime >= '%s' AND a.datetime < '%s' GROUP BY `metric`,`type`,`year`,`month`,`day`,`metric_id` ON DUPLICATE KEY UPDATE `value`=`value`+VALUES(`value`)", $metric, $table_column, $start_datetime, $end_datetime);
        if (!$this->run_db_query($sql))
            return false;

        $table_column = $this->metric_index_2[$metric];
        $sql = sprintf("INSERT INTO statistics_daily(`metric`, `type`, `year`, `month`, `day`, `metric_id`, `value`, `datetime`) SELECT %d AS metric, 0 AS type, YEAR(a.datetime) AS year, MONTH(a.datetime) AS month, DAYOFMONTH(a.datetime) AS day, %s AS metric_id, COUNT(*) AS value, CONCAT(YEAR(a.datetime), '-', MONTH(a.datetime), '-', DAYOFMONTH(a.datetime), ' 00:00:00') AS dt FROM activity AS a WHERE a.datetime >= '%s' AND a.datetime < '%s' GROUP BY `metric`,`type`,`year`,`month`,`day`,`metric_id` ON DUPLICATE KEY UPDATE `value`=`value`+VALUES(`value`)", $metric, $table_column, $start_datetime, $end_datetime);
        if (!$this->run_db_query($sql))
            return false;

        return true;
    }

    private function run_monthly_aggregation_query($metric, $start_datetime, $end_datetime) {
        if(!array_key_exists($metric, $this->metric_index))
            return false;

        $table_column = $this->metric_index[$metric];

        $sql = sprintf("INSERT INTO statistics_monthly(`metric`, `type`, `year`, `month`, `metric_id`, `value`, `datetime`) SELECT %d AS metric, a.type, YEAR(b.datetime) AS year, MONTH(b.datetime) AS month, %s AS metric_id, COUNT(*) AS value, CONCAT(YEAR(b.datetime), '-', MONTH(b.datetime), '-00 00:00:00') AS dt FROM transactions AS a, activity AS b WHERE a.activity_id = b.id AND a.activity_id IS NOT NULL AND a.datetime >= '%s' AND a.datetime < '%s' GROUP BY `metric`,`type`,`year`,`month`,`metric_id` ON DUPLICATE KEY UPDATE `value`=`value`+VALUES(`value`)", $metric, $table_column, $start_datetime, $end_datetime);
        if (!$this->run_db_query($sql))
            return false;

        $table_column = $this->metric_index_2[$metric];
        $sql = sprintf("INSERT INTO statistics_monthly(`metric`, `type`, `year`, `month`, `metric_id`, `value`, `datetime`) SELECT %d AS metric, 0 AS type, YEAR(a.datetime) AS year, MONTH(a.datetime) AS month, %s AS metric_id, COUNT(*) AS value, CONCAT(YEAR(a.datetime), '-', MONTH(a.datetime), '-00 00:00:00') AS dt FROM activity AS a WHERE a.datetime >= '%s' AND a.datetime < '%s' GROUP BY `metric`,`type`,`year`,`month`,`metric_id` ON DUPLICATE KEY UPDATE `value`=`value`+VALUES(`value`)", $metric, $table_column, $start_datetime, $end_datetime);
        if (!$this->run_db_query($sql))
            return false;

        return true;
    }

    private function init_cron_runtime_table($init_datetime) {
        // Setup the table that stores the statistics cron run time
        if(!$this->run_db_query(sprintf('INSERT INTO statistics_cron_runtime VALUES("minutely", "%s", NULL)', $init_datetime)))
            return $this->rollback_trx();

        if(!$this->run_db_query(sprintf('INSERT INTO statistics_cron_runtime VALUES("hourly", "%s", NULL)', $init_datetime)))
            return $this->rollback_trx();

        if(!$this->run_db_query(sprintf('INSERT INTO statistics_cron_runtime VALUES("daily", "%s", NULL)', $init_datetime)))
            return $this->rollback_trx();

        if(!$this->run_db_query(sprintf('INSERT INTO statistics_cron_runtime VALUES("monthly", "%s", NULL)', $init_datetime)))
            return $this->rollback_trx();

        return true;
    }

    private function update_cron_runtime_table($end_datetime) {
        if (!$this->run_db_query(sprintf("UPDATE statistics_cron_runtime SET end='%s' WHERE type='minutely'", $end_datetime)))
            return $this->rollback_trx();

        if (!$this->run_db_query(sprintf("UPDATE statistics_cron_runtime SET end='%s' WHERE type='hourly'", $end_datetime)))
            return $this->rollback_trx();

        if (!$this->run_db_query(sprintf("UPDATE statistics_cron_runtime SET end='%s' WHERE type='daily'", $end_datetime)))
            return $this->rollback_trx();

        if (!$this->run_db_query(sprintf("UPDATE statistics_cron_runtime SET end='%s' WHERE type='monthly'", $end_datetime)))
            return $this->rollback_trx();

        return true;
    }
}

// this is where we do the actual work
try {
    $prepopulator = new Stats_prepopulate(Config::$log_level);
    $prepopulator->do_cleanup();
    $prepopulator->do_populate();
} catch (Exception $e) {
    echo "\nException: \n", $e->getMessage(), "\n";
}

?>
