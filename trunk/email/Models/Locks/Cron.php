<?php

class Locks_Cron extends Database
{

    protected $identifier;
    protected $locked;
    protected $datetime;
    protected $runCount;
    protected $lastStarted;
    protected $lastFinished;

    protected $tableName = 'locks_cron';
    const      tableName = 'locks_cron';

    public function __construct($identifier)
    {
        parent::__construct();

        $sql  = "SELECT * FROM `$this->tableName` WHERE `identifier` = '" . $identifier . "';";

        $result = $this->getArrayAssoc($sql);

        $this->identifier   = $identifier;
        $this->locked       = $result['locked'];
        $this->datetime     = $result['datetime'];
        $this->runCount     = $result['run_count'];
        $this->lastStarted  = $result['last_started'];
        $this->lastFinished = $result['last_finished'];
    }
    //--------------------------------------------------------------------------


    public static function setLock($identifier)
    {
        $db = new Database;

        $sql = "UPDATE `" . self::tableName . "` SET `locked` = '1', `datetime` = NOW(), `last_started` = NOW() WHERE `identifier` = '" . mysql_real_escape_string($identifier) . "' LIMIT 1;";
        $db->query($sql);

        return true;
    }
    //--------------------------------------------------------------------------


    public static function removeLock($identifier)
    {
        $db = new Database;

        $sql = "UPDATE `" . self::tableName . "` SET `locked` = '0', `datetime` = NULL, `last_finished` = NOW(), `run_count` = `run_count` + 1 WHERE `identifier` = '" . mysql_real_escape_string($identifier) . "' LIMIT 1;";
        $db->query($sql);

        return true;
    }
    //--------------------------------------------------------------------------


    public static function getCronsWithLocks($interval = Config::CRON_TIMEOUT)
    {
        $db = new Database;

        $currentDate = new Datetime();
        $currentDate->sub(new Dateinterval('PT' . $interval . 'S'));
        $cutOff      = $currentDate->format('Y-m-d H:i:s');

        $sql  = "SELECT `identifier` FROM `" . self::tableName . "`";
        $sql .= " WHERE `locked` = '1' AND `datetime` < '" . $cutOff . "'";

        $result = $db->getArray($sql);

        return $result;
    }
    //--------------------------------------------------------------------------


    public static function isLocked($identifier)
    {
        $db = new Database;

        $sql = "SELECT `locked` FROM `" . self::tableName . "` WHERE `identifier` = '" . mysql_real_escape_string($identifier) . "' LIMIT 1;";
        $result = $db->getUpperLeft($sql);

        if ($result == 1) {
            return true;
        } else {
            return false;
        }
    }
    //--------------------------------------------------------------------------
}