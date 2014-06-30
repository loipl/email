<?php

class Statistic extends Database
{

    protected $metric;
    protected $type;
    protected $year;
    protected $month;
    protected $day;
    protected $hour;
    protected $minute;
    protected $metric_id;
    protected $value;

    private static $defaultInterval = 'day';

    const      tableName = 'statistics';
    const      hourlyTableName = 'statistics_hourly';
    const      dailyTableName = 'statistics_daily';
    const      monthlyTableName = 'statistics_monthly';

    public function __construct($metric, $type, $id, $interval, $start, $end)
    {
        parent::__construct();
    }
    //--------------------------------------------------------------------------


    public static function getTotals($metric, $type, $id, $interval, $start, $end)
    {
        $db = new Database;
        $result = array();

        $fieldList = self::getFieldsFromInterval($interval);
        $fields = '';

        foreach ($fieldList AS $singleField) {
            $fields .= '`' . $singleField . '`, ';
        }

        $fields = substr($fields, 0, -2);

        if($interval == 'minute') {
            $table = self::tableName;
        } elseif($interval == 'hour') {
            $table = self::hourlyTableName;
        } elseif($interval == 'day') {
            $table = self::dailyTableName;
        } else {
            $table = self::monthlyTableName;
        }

        $sql  = "SELECT " . mysql_real_escape_string($fields) . ", metric_id, SUM(value) FROM `" . $table . "` WHERE";
        $sql .= "     `metric` = '" . mysql_real_escape_string(self::getMetricIdFromName($metric)) . "'";
        $sql .= " AND `type`   = '" . mysql_real_escape_string(self::getTypeIdFromName($type))     . "'";

        if (!empty($id)) {
            $sql .=  " AND `metric_id` = '" . mysql_real_escape_string($id) . "'";
        }

        if (!empty($start)) {
            $sql .= " AND datetime >= '" . $start . "'";
        }

        if (!empty($end)) {
            $sql .= " AND datetime <= '" . $end . "'";
        }

        $sql .= " GROUP BY " . $fields . ", metric_id";

        $result = $db->getArray($sql);

        return $result;
    }
    //--------------------------------------------------------------------------


    private static function getFieldsFromInterval($interval = NULL)
    {
        if (empty($interval)) {
            $interval = self::$defaultInterval;
        }

        $fields = array_reverse(Config::$validIntervals);
        $intervalPosition = array_search($interval, $fields);
        $fields = array_reverse(array_slice($fields, $intervalPosition));

        return $fields;
    }
    //--------------------------------------------------------------------------


    private static function getMetricIdFromName($metric)
    {
        $id = array_search($metric, Config::$validMetrics);

        if (!is_null($id)) {
            return $id;
        } else {
            return false;
        }
    }
    //--------------------------------------------------------------------------


    private static function getTypeIdFromName($type)
    {
        $id = array_search($type, Config::$validTypes);

        if (!is_null($id)) {
            return $id;
        } else {
            return false;
        }
    }
    //--------------------------------------------------------------------------


    public static function cronDailyProcess()
    {
        $db = new Database;

        $sql = "REPLACE INTO statistics SELECT 1 AS metric, a.type, YEAR(b.datetime) AS year, MONTH(b.datetime) AS month, DAYOFMONTH(b.datetime) AS day, HOUR(b.datetime) AS hour, MINUTE(b.datetime) AS minute, b.campaign_id AS metric_id, COUNT(*) AS value FROM transactions AS a, activity AS b WHERE a.activity_id = b.id AND a.activity_id IS NOT NULL AND a.datetime > DATE_SUB(NOW(),INTERVAL 7 DAY) AND a.datetime < DATE_SUB(NOW(),INTERVAL 1 DAY) GROUP BY `metric_id`, `minute`, `hour`, `day`, `month`, `year`, a.type, `metric`;";
        $db->query($sql);

        $sql = "REPLACE INTO statistics SELECT 2 AS metric, a.type, YEAR(b.datetime) AS year, MONTH(b.datetime) AS month, DAYOFMONTH(b.datetime) AS day, HOUR(b.datetime) AS hour, MINUTE(b.datetime) AS minute, b.creative_id AS metric_id, COUNT(*) AS value FROM transactions AS a, activity AS b WHERE a.activity_id = b.id AND a.activity_id IS NOT NULL AND a.datetime > DATE_SUB(NOW(),INTERVAL 7 DAY) AND a.datetime < DATE_SUB(NOW(),INTERVAL 1 DAY) GROUP BY `metric_id`, `minute`, `hour`, `day`, `month`, `year`, a.type, `metric`;";
        $db->query($sql);

        $sql = "REPLACE INTO statistics SELECT 3 AS metric, a.type, YEAR(b.datetime) AS year, MONTH(b.datetime) AS month, DAYOFMONTH(b.datetime) AS day, HOUR(b.datetime) AS hour, MINUTE(b.datetime) AS minute, b.sender AS metric_id, COUNT(*) AS value FROM transactions AS a, activity AS b WHERE a.activity_id = b.id AND a.activity_id IS NOT NULL AND a.datetime > DATE_SUB(NOW(),INTERVAL 7 DAY) AND a.datetime < DATE_SUB(NOW(),INTERVAL 1 DAY) GROUP BY `metric_id`, `minute`, `hour`, `day`, `month`, `year`, a.type, `metric`;";
        $db->query($sql);

        $sql = "REPLACE INTO statistics SELECT 4 AS metric, a.type, YEAR(b.datetime) AS year, MONTH(b.datetime) AS month, DAYOFMONTH(b.datetime) AS day, HOUR(b.datetime) AS hour, MINUTE(b.datetime) AS minute, b.channel AS metric_id, COUNT(*) AS value FROM transactions AS a, activity AS b WHERE a.activity_id = b.id AND a.activity_id IS NOT NULL AND a.datetime > DATE_SUB(NOW(),INTERVAL 7 DAY) AND a.datetime < DATE_SUB(NOW(),INTERVAL 1 DAY) GROUP BY `metric_id`, `minute`, `hour`, `day`, `month`, `year`, a.type, `metric`;";
        $db->query($sql);

        $sql = "REPLACE INTO statistics SELECT 5 AS metric, a.type, YEAR(b.datetime) AS year, MONTH(b.datetime) AS month, DAYOFMONTH(b.datetime) AS day, HOUR(b.datetime) AS hour, MINUTE(b.datetime) AS minute, SUBSTRING_INDEX(b.email, '@', -1) AS metric_id, COUNT(*) AS value FROM transactions AS a, activity AS b WHERE a.activity_id = b.id AND a.activity_id IS NOT NULL AND a.datetime > DATE_SUB(NOW(),INTERVAL 7 DAY) AND a.datetime < DATE_SUB(NOW(),INTERVAL 1 DAY) GROUP BY `metric_id`, `minute`, `hour`, `day`, `month`, `year`, a.type, `metric`;";
        $db->query($sql);

        $sql = "REPLACE INTO statistics SELECT 1 AS metric, 0 AS type, YEAR(a.datetime) AS year, MONTH(a.datetime) AS month, DAYOFMONTH(a.datetime) AS day, HOUR(a.datetime) AS hour, MINUTE(a.datetime) AS minute, a.campaign_id AS metric_id, COUNT(*) AS value FROM activity AS a WHERE a.datetime > DATE_SUB(NOW(),INTERVAL 7 DAY) AND a.datetime < DATE_SUB(NOW(),INTERVAL 1 DAY) GROUP BY `metric_id`, `minute`, `hour`, `day`, `month`, `year`, `type`, `metric`;";
        $db->query($sql);

        $sql = "REPLACE INTO statistics SELECT 2 AS metric, 0 AS type, YEAR(a.datetime) AS year, MONTH(a.datetime) AS month, DAYOFMONTH(a.datetime) AS day, HOUR(a.datetime) AS hour, MINUTE(a.datetime) AS minute, a.creative_id AS metric_id, COUNT(*) AS value FROM activity AS a WHERE a.datetime > DATE_SUB(NOW(),INTERVAL 7 DAY) AND a.datetime < DATE_SUB(NOW(),INTERVAL 1 DAY) GROUP BY `metric_id`, `minute`, `hour`, `day`, `month`, `year`, `type`, `metric`;";
        $db->query($sql);

        $sql = "REPLACE INTO statistics SELECT 3 AS metric, 0 AS type, YEAR(a.datetime) AS year, MONTH(a.datetime) AS month, DAYOFMONTH(a.datetime) AS day, HOUR(a.datetime) AS hour, MINUTE(a.datetime) AS minute, a.sender AS metric_id, COUNT(*) AS value FROM activity AS a WHERE a.datetime > DATE_SUB(NOW(),INTERVAL 7 DAY) AND a.datetime < DATE_SUB(NOW(),INTERVAL 1 DAY) GROUP BY `metric_id`, `minute`, `hour`, `day`, `month`, `year`, `type`, `metric`;";
        $db->query($sql);

        $sql = "REPLACE INTO statistics SELECT 4 AS metric, 0 AS type, YEAR(a.datetime) AS year, MONTH(a.datetime) AS month, DAYOFMONTH(a.datetime) AS day, HOUR(a.datetime) AS hour, MINUTE(a.datetime) AS minute, a.channel AS metric_id, COUNT(*) AS value FROM activity AS a WHERE a.datetime > DATE_SUB(NOW(),INTERVAL 7 DAY) AND a.datetime < DATE_SUB(NOW(),INTERVAL 1 DAY) GROUP BY `metric_id`, `minute`, `hour`, `day`, `month`, `year`, `type`, `metric`;";
        $db->query($sql);

        $sql = "REPLACE INTO statistics SELECT 5 AS metric, 0 AS type, YEAR(a.datetime) AS year, MONTH(a.datetime) AS month, DAYOFMONTH(a.datetime) AS day, HOUR(a.datetime) AS hour, MINUTE(a.datetime) AS minute, SUBSTRING_INDEX(a.email, '@', -1) AS metric_id, COUNT(*) AS value FROM activity AS a WHERE a.datetime > DATE_SUB(NOW(),INTERVAL 7 DAY) AND a.datetime < DATE_SUB(NOW(),INTERVAL 1 DAY) GROUP BY `metric_id`, `minute`, `hour`, `day`, `month`, `year`, `type`, `metric`;";
        $db->query($sql);

        $sql = "REPLACE INTO statistics (metric, type, year, month, day, hour, metric_id, value) SELECT a.metric, a.type, a.year, a.month, a.day, a.hour, a.metric_id, SUM(a.value) AS value FROM statistics AS a WHERE STR_TO_DATE(CONCAT(a.year,'-',a.month,'-',a.day), '%Y-%m-%d') < DATE_SUB(NOW(),INTERVAL 1 DAY) AND a.hour IS NOT NULL GROUP BY a.metric, a.type, a.year, a.month, a.day, a.hour, a.metric_id;";
        $db->query($sql);

        $sql = "DELETE FROM statistics WHERE STR_TO_DATE(CONCAT(year,'-',month,'-',day), '%Y-%m-%d') < DATE_SUB(NOW(),INTERVAL 1 DAY) AND hour IS NOT NULL AND minute IS NOT NULL;";
        $db->query($sql);

        $sql = "REPLACE INTO statistics (metric, type, year, month, day, metric_id, value) SELECT a.metric, a.type, a.year, a.month, a.day, a.metric_id, SUM(a.value) AS value FROM statistics AS a WHERE STR_TO_DATE(CONCAT(a.year,'-',a.month,'-',a.day), '%Y-%m-%d') < DATE_SUB(NOW(),INTERVAL 7 DAY) AND a.minute IS NULL GROUP BY a.metric, a.type, a.year, a.month, a.day, a.metric_id;";
        $db->query($sql);

        $sql = "DELETE FROM statistics WHERE STR_TO_DATE(CONCAT(year,'-',month,'-',day), '%Y-%m-%d') < DATE_SUB(NOW(),INTERVAL 7 DAY) AND hour IS NOT NULL AND minute IS NULL;";
        $db->query($sql);

        $sql = "REPLACE INTO statistics (metric, type, year, month, metric_id, value) SELECT a.metric, a.type, a.year, a.month, a.metric_id, SUM(a.value) as value FROM statistics AS a WHERE STR_TO_DATE(CONCAT(a.year,'-',a.month,'-',a.day), '%Y-%m-%d') < DATE_SUB(NOW(),INTERVAL 30 DAY) AND a.hour IS NULL AND minute IS NULL OR a.day IS NULL GROUP BY a.metric, a.type, a.year, a.month, a.metric_id;";
        $db->query($sql);

        $sql = "DELETE FROM statistics WHERE STR_TO_DATE(CONCAT(year,'-',month,'-',day), '%Y-%m-%d') < DATE_SUB(NOW(),INTERVAL 30 DAY) AND hour IS NULL AND minute IS NULL;";
        $db->query($sql);
    }
    //--------------------------------------------------------------------------


    public static function cronHourlyProcess()
    {
        $db = new Database;

        $sql = "REPLACE INTO statistics SELECT 1 AS metric, a.type, YEAR(b.datetime) AS year, MONTH(b.datetime) AS month, DAYOFMONTH(b.datetime) AS day, HOUR(b.datetime) AS hour, MINUTE(b.datetime) AS minute, b.campaign_id AS metric_id, COUNT(*) AS value FROM transactions AS a, activity AS b WHERE a.activity_id = b.id AND a.activity_id IS NOT NULL AND a.datetime > DATE_SUB(NOW(),INTERVAL 24 HOUR) AND a.datetime < DATE_SUB(NOW(),INTERVAL 1 HOUR) GROUP BY `metric_id`, `minute`, `hour`, `day`, `month`, `year`, a.type, `metric`;";
        $db->query($sql);

        $sql = "REPLACE INTO statistics SELECT 2 AS metric, a.type, YEAR(b.datetime) AS year, MONTH(b.datetime) AS month, DAYOFMONTH(b.datetime) AS day, HOUR(b.datetime) AS hour, MINUTE(b.datetime) AS minute, b.creative_id AS metric_id, COUNT(*) AS value FROM transactions AS a, activity AS b WHERE a.activity_id = b.id AND a.activity_id IS NOT NULL AND a.datetime > DATE_SUB(NOW(),INTERVAL 24 HOUR) AND a.datetime < DATE_SUB(NOW(),INTERVAL 1 HOUR) GROUP BY `metric_id`, `minute`, `hour`, `day`, `month`, `year`, a.type, `metric`;";
        $db->query($sql);

        $sql = "REPLACE INTO statistics SELECT 3 AS metric, a.type, YEAR(b.datetime) AS year, MONTH(b.datetime) AS month, DAYOFMONTH(b.datetime) AS day, HOUR(b.datetime) AS hour, MINUTE(b.datetime) AS minute, b.sender AS metric_id, COUNT(*) AS value FROM transactions AS a, activity AS b WHERE a.activity_id = b.id AND a.activity_id IS NOT NULL AND a.datetime > DATE_SUB(NOW(),INTERVAL 24 HOUR) AND a.datetime < DATE_SUB(NOW(),INTERVAL 1 HOUR) GROUP BY `metric_id`, `minute`, `hour`, `day`, `month`, `year`, a.type, `metric`;";
        $db->query($sql);

        $sql = "REPLACE INTO statistics SELECT 4 AS metric, a.type, YEAR(b.datetime) AS year, MONTH(b.datetime) AS month, DAYOFMONTH(b.datetime) AS day, HOUR(b.datetime) AS hour, MINUTE(b.datetime) AS minute, b.channel AS metric_id, COUNT(*) AS value FROM transactions AS a, activity AS b WHERE a.activity_id = b.id AND a.activity_id IS NOT NULL AND a.datetime > DATE_SUB(NOW(),INTERVAL 24 HOUR) AND a.datetime < DATE_SUB(NOW(),INTERVAL 1 HOUR) GROUP BY `metric_id`, `minute`, `hour`, `day`, `month`, `year`, a.type, `metric`;";
        $db->query($sql);

        $sql = "REPLACE INTO statistics SELECT 5 AS metric, a.type, YEAR(b.datetime) AS year, MONTH(b.datetime) AS month, DAYOFMONTH(b.datetime) AS day, HOUR(b.datetime) AS hour, MINUTE(b.datetime) AS minute, SUBSTRING_INDEX(b.email, '@', -1) AS metric_id, COUNT(*) AS value FROM transactions AS a, activity AS b WHERE a.activity_id = b.id AND a.activity_id IS NOT NULL AND a.datetime > DATE_SUB(NOW(),INTERVAL 24 HOUR) AND a.datetime < DATE_SUB(NOW(),INTERVAL 1 HOUR) GROUP BY `metric_id`, `minute`, `hour`, `day`, `month`, `year`, a.type, `metric`;";
        $db->query($sql);

        $sql = "REPLACE INTO statistics SELECT 1 AS metric, 0 AS type, YEAR(a.datetime) AS year, MONTH(a.datetime) AS month, DAYOFMONTH(a.datetime) AS day, HOUR(a.datetime) AS hour, MINUTE(a.datetime) AS minute, a.campaign_id AS metric_id, COUNT(*) AS value FROM activity AS a WHERE a.datetime > DATE_SUB(NOW(),INTERVAL 24 HOUR) AND a.datetime < DATE_SUB(NOW(),INTERVAL 1 HOUR) GROUP BY `metric_id`, `minute`, `hour`, `day`, `month`, `year`, `type`, `metric`;";
        $db->query($sql);

        $sql = "REPLACE INTO statistics SELECT 2 AS metric, 0 AS type, YEAR(a.datetime) AS year, MONTH(a.datetime) AS month, DAYOFMONTH(a.datetime) AS day, HOUR(a.datetime) AS hour, MINUTE(a.datetime) AS minute, a.creative_id AS metric_id, COUNT(*) AS value FROM activity AS a WHERE a.datetime > DATE_SUB(NOW(),INTERVAL 24 HOUR) AND a.datetime < DATE_SUB(NOW(),INTERVAL 1 HOUR) GROUP BY `metric_id`, `minute`, `hour`, `day`, `month`, `year`, `type`, `metric`;";
        $db->query($sql);

        $sql = "REPLACE INTO statistics SELECT 3 AS metric, 0 AS type, YEAR(a.datetime) AS year, MONTH(a.datetime) AS month, DAYOFMONTH(a.datetime) AS day, HOUR(a.datetime) AS hour, MINUTE(a.datetime) AS minute, a.sender AS metric_id, COUNT(*) AS value FROM activity AS a WHERE a.datetime > DATE_SUB(NOW(),INTERVAL 24 HOUR) AND a.datetime < DATE_SUB(NOW(),INTERVAL 1 HOUR) GROUP BY `metric_id`, `minute`, `hour`, `day`, `month`, `year`, `type`, `metric`;";
        $db->query($sql);

        $sql = "REPLACE INTO statistics SELECT 4 AS metric, 0 AS type, YEAR(a.datetime) AS year, MONTH(a.datetime) AS month, DAYOFMONTH(a.datetime) AS day, HOUR(a.datetime) AS hour, MINUTE(a.datetime) AS minute, a.channel AS metric_id, COUNT(*) AS value FROM activity AS a WHERE a.datetime > DATE_SUB(NOW(),INTERVAL 24 HOUR) AND a.datetime < DATE_SUB(NOW(),INTERVAL 1 HOUR) GROUP BY `metric_id`, `minute`, `hour`, `day`, `month`, `year`, `type`, `metric`;";
        $db->query($sql);

        $sql = "REPLACE INTO statistics SELECT 5 AS metric, 0 AS type, YEAR(a.datetime) AS year, MONTH(a.datetime) AS month, DAYOFMONTH(a.datetime) AS day, HOUR(a.datetime) AS hour, MINUTE(a.datetime) AS minute, SUBSTRING_INDEX(a.email, '@', -1) AS metric_id, COUNT(*) AS value FROM activity AS a WHERE a.datetime > DATE_SUB(NOW(),INTERVAL 24 HOUR) AND a.datetime < DATE_SUB(NOW(),INTERVAL 1 HOUR) GROUP BY `metric_id`, `minute`, `hour`, `day`, `month`, `year`, `type`, `metric`;";
        $db->query($sql);
    }
    //--------------------------------------------------------------------------


    public static function cronMinutelyProcess()
    {
        $db = new Database;

        $sql = "REPLACE INTO statistics SELECT 1 AS metric, a.type, YEAR(b.datetime) AS year, MONTH(b.datetime) AS month, DAYOFMONTH(b.datetime) AS day, HOUR(b.datetime) AS hour, MINUTE(b.datetime) AS minute, b.campaign_id AS metric_id, COUNT(*) AS value FROM transactions AS a, activity AS b WHERE a.activity_id = b.id AND a.activity_id IS NOT NULL AND a.datetime > DATE_SUB(NOW(),INTERVAL 1 HOUR) GROUP BY `metric_id`, `minute`, `hour`, `day`, `month`, `year`, a.type, `metric`;";
        $db->query($sql);

        $sql = "REPLACE INTO statistics SELECT 2 AS metric, a.type, YEAR(b.datetime) AS year, MONTH(b.datetime) AS month, DAYOFMONTH(b.datetime) AS day, HOUR(b.datetime) AS hour, MINUTE(b.datetime) AS minute, b.creative_id AS metric_id, COUNT(*) AS value FROM transactions AS a, activity AS b WHERE a.activity_id = b.id AND a.activity_id IS NOT NULL AND a.datetime > DATE_SUB(NOW(),INTERVAL 1 HOUR) GROUP BY `metric_id`, `minute`, `hour`, `day`, `month`, `year`, a.type, `metric`;";
        $db->query($sql);

        $sql = "REPLACE INTO statistics SELECT 3 AS metric, a.type, YEAR(b.datetime) AS year, MONTH(b.datetime) AS month, DAYOFMONTH(b.datetime) AS day, HOUR(b.datetime) AS hour, MINUTE(b.datetime) AS minute, b.sender AS metric_id, COUNT(*) AS value FROM transactions AS a, activity AS b WHERE a.activity_id = b.id AND a.activity_id IS NOT NULL AND a.datetime > DATE_SUB(NOW(),INTERVAL 1 HOUR) GROUP BY `metric_id`, `minute`, `hour`, `day`, `month`, `year`, a.type, `metric`;";
        $db->query($sql);

        $sql = "REPLACE INTO statistics SELECT 4 AS metric, a.type, YEAR(b.datetime) AS year, MONTH(b.datetime) AS month, DAYOFMONTH(b.datetime) AS day, HOUR(b.datetime) AS hour, MINUTE(b.datetime) AS minute, b.channel AS metric_id, COUNT(*) AS value FROM transactions AS a, activity AS b WHERE a.activity_id = b.id AND a.activity_id IS NOT NULL AND a.datetime > DATE_SUB(NOW(),INTERVAL 1 HOUR) GROUP BY `metric_id`, `minute`, `hour`, `day`, `month`, `year`, a.type, `metric`;";
        $db->query($sql);

        $sql = "REPLACE INTO statistics SELECT 5 AS metric, a.type, YEAR(b.datetime) AS year, MONTH(b.datetime) AS month, DAYOFMONTH(b.datetime) AS day, HOUR(b.datetime) AS hour, MINUTE(b.datetime) AS minute, SUBSTRING_INDEX(b.email, '@', -1) AS metric_id, COUNT(*) AS value FROM transactions AS a, activity AS b WHERE a.activity_id = b.id AND a.activity_id IS NOT NULL AND a.datetime > DATE_SUB(NOW(),INTERVAL 1 HOUR) GROUP BY `metric_id`, `minute`, `hour`, `day`, `month`, `year`, a.type, `metric`;";
        $db->query($sql);

        $sql = "REPLACE INTO statistics SELECT 1 AS metric, 0 AS type, YEAR(a.datetime) AS year, MONTH(a.datetime) AS month, DAYOFMONTH(a.datetime) AS day, HOUR(a.datetime) AS hour, MINUTE(a.datetime) AS minute, a.campaign_id AS metric_id, COUNT(*) AS value FROM activity AS a WHERE a.datetime > DATE_SUB(NOW(),INTERVAL 1 HOUR) GROUP BY `metric_id`, `minute`, `hour`, `day`, `month`, `year`, `type`, `metric`;";
        $db->query($sql);

        $sql = "REPLACE INTO statistics SELECT 2 AS metric, 0 AS type, YEAR(a.datetime) AS year, MONTH(a.datetime) AS month, DAYOFMONTH(a.datetime) AS day, HOUR(a.datetime) AS hour, MINUTE(a.datetime) AS minute, a.creative_id AS metric_id, COUNT(*) AS value FROM activity AS a WHERE a.datetime > DATE_SUB(NOW(),INTERVAL 1 HOUR) GROUP BY `metric_id`, `minute`, `hour`, `day`, `month`, `year`, `type`, `metric`;";
        $db->query($sql);

        $sql = "REPLACE INTO statistics SELECT 3 AS metric, 0 AS type, YEAR(a.datetime) AS year, MONTH(a.datetime) AS month, DAYOFMONTH(a.datetime) AS day, HOUR(a.datetime) AS hour, MINUTE(a.datetime) AS minute, a.sender AS metric_id, COUNT(*) AS value FROM activity AS a WHERE a.datetime > DATE_SUB(NOW(),INTERVAL 1 HOUR) GROUP BY `metric_id`, `minute`, `hour`, `day`, `month`, `year`, `type`, `metric`;";
        $db->query($sql);

        $sql = "REPLACE INTO statistics SELECT 4 AS metric, 0 AS type, YEAR(a.datetime) AS year, MONTH(a.datetime) AS month, DAYOFMONTH(a.datetime) AS day, HOUR(a.datetime) AS hour, MINUTE(a.datetime) AS minute, a.channel AS metric_id, COUNT(*) AS value FROM activity AS a WHERE a.datetime > DATE_SUB(NOW(),INTERVAL 1 HOUR) GROUP BY `metric_id`, `minute`, `hour`, `day`, `month`, `year`, `type`, `metric`;";
        $db->query($sql);

        $sql = "REPLACE INTO statistics SELECT 5 AS metric, 0 AS type, YEAR(a.datetime) AS year, MONTH(a.datetime) AS month, DAYOFMONTH(a.datetime) AS day, HOUR(a.datetime) AS hour, MINUTE(a.datetime) AS minute, SUBSTRING_INDEX(a.email, '@', -1) AS metric_id, COUNT(*) AS value FROM activity AS a WHERE a.datetime > DATE_SUB(NOW(),INTERVAL 1 HOUR) GROUP BY `metric_id`, `minute`, `hour`, `day`, `month`, `year`, `type`, `metric`;";
        $db->query($sql);
    }
    //--------------------------------------------------------------------------
}