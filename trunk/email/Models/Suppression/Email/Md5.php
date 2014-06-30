<?php

class Suppression_Email_Md5 extends Database
{

    protected $email;
    protected $source;
    protected $reason;

    protected $tableName = 'suppression_email_md5';
    const      tableName = 'suppression_email_md5';

    public function __construct()
    {
        parent::__construct();
    }
    //--------------------------------------------------------------------------


    public static function addEmailMd5Suppression($emailMd5, $source, $reason)
    {
        $db = new Database;

        $sql  = "INSERT IGNORE INTO `" . self::tableName . "` (email_md5, source, reason) VALUES (";
        $sql .= "'" . mysql_real_escape_string($emailMd5) . "', '" . $source . "', '" . $reason . "')";

        $db->query($sql);
    }
    //--------------------------------------------------------------------------
}