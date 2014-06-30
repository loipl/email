<?php

class Suppression_Domain_Md5 extends Database
{

    protected $domain;
    protected $source;
    protected $reason;

    protected $tableName = 'suppression_domain_md5';
    const      tableName = 'suppression_domain_md5';

    public function __construct()
    {
        parent::__construct();
    }
    //--------------------------------------------------------------------------


    public static function addDomainMd5Suppression($domainMd5, $source, $reason)
    {
        $db = new Database;

        $sql  = "INSERT IGNORE INTO `" . self::tableName . "` (domain_md5, source, reason) VALUES (";
        $sql .= "'" . mysql_real_escape_string($domainMd5) . "', '" . $source . "', '" . $reason . "')";

        $db->query($sql);
    }
    //--------------------------------------------------------------------------
}