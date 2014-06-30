<?php

class Suppression_Domain extends Database
{

    protected $domain;
    protected $source;
    protected $reason;

    protected $tableName = 'suppression_domain';
    const      tableName = 'suppression_domain';

    public function __construct()
    {
        parent::__construct();
    }
    //--------------------------------------------------------------------------


    public static function addDomainSuppression($domain, $source, $reason)
    {
        $db = new Database;

        $sql  = "INSERT IGNORE INTO `" . self::tableName . "` (domain, source, reason) VALUES (";
        $sql .= "'" . mysql_real_escape_string($domain) . "', '" . $source . "', '" . $reason . "')";

        $db->query($sql);
    }
    //--------------------------------------------------------------------------
}