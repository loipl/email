<?php

class CacheDataAppend extends Database
{

    protected $id;
    protected $key;
    protected $email;
    protected $query;
    protected $result;
    protected $datetime;

    protected $tableName = 'cache_data_append';
    const      tableName = 'cache_data_append';

    public function __construct($email) {
        parent::__construct();

        $sql  = "SELECT * FROM `$this->tableName` WHERE `email` = '" . mysql_real_escape_string($email) . "';";

        $result = $this->getArrayAssoc($sql);

        $this->id     = $result['id'];
        $this->key    = $result['key'];
        $this->email  = $result['email'];
        $this->query  = $result['query'];
        $this->result = $result['result'];
    }
    //--------------------------------------------------------------------------


    public function getDatetime()
    {
        return $this->$datetime;
    }
    //--------------------------------------------------------------------------


    public function getResult()
    {
        return $this->$result;
    }
    //--------------------------------------------------------------------------
}