<?php

class User extends Database
{

    protected $id;
    protected $username;
    protected $password;
    protected $email;
    protected $displayName;
    protected $status;
    protected $registered;

    protected $tableName = 'users';
    const      tableName = 'users';

    public function __construct($id)
    {
        parent::__construct();

        $sql  = "SELECT * FROM `$this->tableName` WHERE `id` = '" . $id . "';";

        $result = $this->getArrayAssoc($sql);

        $this->id           = $id;
        $this->username     = $result['domain'];
        $this->password     = $result['campaign_id'];
        $this->email        = $result['creative_id'];
        $this->displayName  = $result['display_name'];
        $this->status       = $result['status'];
        $this->registered   = $result['registered'];
    }
    //--------------------------------------------------------------------------

    public static function checkUserExists($username, $password) 
    {
        $db = new Database;

        $sql  = "SELECT `id` FROM `" . self::tableName . "`";
        $sql .= " WHERE `username`       = '" . mysql_real_escape_string($username). "'";
        $sql .= " AND   `password`       = '" . mysql_real_escape_string($password) . "'";
        $sql .= " LIMIT 1;";

        $result = $db->getUpperLeft($sql);

        if ($result > 0) {
            return true;
        } else {
            return false;
        }
    }
    //--------------------------------------------------------------------------
    
}