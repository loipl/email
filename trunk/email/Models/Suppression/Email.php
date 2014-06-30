<?php

class Suppression_Email extends Database
{

    protected $email;
    protected $source;
    protected $reason;

    protected $tableName = 'suppression_email';
    const      tableName = 'suppression_email';

    public function __construct()
    {
        parent::__construct();
    }
    //--------------------------------------------------------------------------


    public static function addEmailSuppression($email, $source, $reason)
    {
        $db = new Database;

        $sql  = "INSERT IGNORE INTO `" . self::tableName . "` (email, source, reason) VALUES (";
        $sql .= "'" . mysql_real_escape_string($email) . "', '" . $source . "', '" . $reason . "')";

        $db->query($sql);
    }
    //--------------------------------------------------------------------------
}