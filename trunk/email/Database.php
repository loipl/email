<?php

class Database
{

    protected $link;
    protected $connected;
    protected $tableName;

    public function __construct()
    {
        $database = Config::$database;
        
        // using test database for test script
        if (isset($_SERVER['test_environment'])) {
            $database = Config::$testDatabase;
        }
        
        $this->link = mysql_connect($database['host'],
                                    $database['username'],
                                    $database['password']
                                   );

        if (!$this->link) {
            die('Cannot connect to DB server');
        }

        mysql_query('USE `' . $database['database'] . '`', $this->link) or die('Cannot access DB ' . $database['database']);
    }
    //--------------------------------------------------------------------------


    public function getUpperLeft($sql)
    {
        $result = mysql_query($sql, $this->link);

        if (!$result) {
            Logging::logDatabaseError($sql, mysql_error());
        }

        $row = mysql_fetch_row($result);

        return $row[0];
    }
    //--------------------------------------------------------------------------


    public function query($sql)
    {
        $result = mysql_query($sql, $this->link);

        $error       = mysql_error();
        $errorNumber = mysql_errno();

        if (!$error && !$errorNumber) {
            return true;
        } else {
            Logging::logDatabaseError($sql, $errorNumber . ': ' . $error);
            return array('error' => $error, 'error_number' => $errorNumber);
        }
    }
    //--------------------------------------------------------------------------


    public function getArray($sql)
    {
        $result = mysql_query($sql, $this->link);

        if (!$result) {
            Logging::logDatabaseError($sql, mysql_error());
            return false;
        }
        
        while ($row = mysql_fetch_assoc($result)) {
            $data[] = $row;
        }

        if (!empty($data)) {
            return $data;
        } else {
            return false;
        }
    }
    //--------------------------------------------------------------------------


    public function getArrayAssoc($sql)
    {
        $result = mysql_query($sql, $this->link);

        if (!$result) {
            Logging::logDatabaseError($sql, mysql_error());
        }

        $row = mysql_fetch_assoc($result);

        return $row;
    }
    //--------------------------------------------------------------------------


    public function getMySQLVersion()
    {
        return $this->getUpperLeft("SELECT VERSION() as mysql_version");
    }
    //--------------------------------------------------------------------------
    
    
    public function getDatabaseName()
    {
        return $this->getUpperLeft("SELECT DATABASE();");
    }
    //--------------------------------------------------------------------------
}