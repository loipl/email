<?php

class Seed extends Database
{

    protected $host         = null;
    protected $user         = null;
    protected $hostPassword = null;
    protected $port         = 993;
    protected $id;
    protected $email;
    protected $password;
    protected $domain;
    protected $tableName    = 'seeds';

    const tableName = 'seeds';

    public function __construct($email)
    {
        parent::__construct();

        $result = self::getSeed($email);

        $this->host         = $result['host']; // host server name
        $this->user         = $result['user']; // host server user name
        $this->hostPassword = $result['hostPassword']; // host server user password
        $this->port         = $result['port']; // host server connecting port
        $this->id           = $result['id'];  // db auto-increment value
        $this->email        = $result['email']; // seed email
        $this->password     = $result['password']; // seed password
        $this->domain       = $result['domain']; // seed domain
    }

    //--------------------------------------------------------------------------

    public static function addSeed($email, $password, $host, $user, $hostPassword, $port)
    {
        $db = new Database;

        $domainStarPos = strrchr($email, "@");

        $domainEndPos = strpos($domainStarPos, ".");

        $domain = substr($domainStarPos, 1, $domainEndPos - 1);

        $sql = "INSERT INTO `" . self::tableName . "` (email, password, domain, host, user, hostPassword, port) VALUES ('" . mysql_real_escape_string($email) . "','" . mysql_real_escape_string(md5($password)) . "','" . mysql_real_escape_string($domain) . "','" . mysql_real_escape_string($host) . "','" . mysql_real_escape_string($user) . "','" . mysql_real_escape_string(md5($hostPassword)) . "','" . mysql_real_escape_string($port) . "')";

        $result = $db->query($sql);

        return $result;
    }

    //--------------------------------------------------------------------------

    public static function getSeed($email)
    {
        $db = new Database;

        $sql = "SELECT id,email,password,domain,host,user,hostPassword,port FROM `" . self::tableName . "` WHERE `email` = '" . mysql_real_escape_string($email) . "'";

        $result = $db->getArray($sql);

        return $result;
    }

    //--------------------------------------------------------------------------

    public static function updateSeed($email, $password, $host, $user, $hostPassword, $port)
    {
        $db = new Database;

        $sql = "UPDATE `" . self::tableName . "` SET password='" . mysql_real_escape_string(md5($password)) . "',"
                . "host='" . mysql_real_escape_string($host) . "',"
                . "user='" . mysql_real_escape_string($user) . "',"
                . "hostPassword='" . mysql_real_escape_string(md5($hostPassword)) . "',"
                . "port='" . mysql_real_escape_string($port) . "'  WHERE email='" . $email . "'";

        $result = $db->query($sql);

        return $result;
    }

    //--------------------------------------------------------------------------

    public static function deleteSeed($id)
    {
        $db = new Database;

        $sql = "DELETE FROM `" . self::tableName . "` WHERE id='" . $id . "'";

        $result = $db->query($sql);

        return $result;
    }

    //--------------------------------------------------------------------------

    public static function getSeedsByDomain($domain, $totalReterive = 1, $orderBy = 'ASC', $offset = 0)
    {
        $db = new Database;

        $sql = "SELECT * FROM `" . self::tableName . "` WHERE domain = '" . mysql_real_escape_string($domain) . "' ORDER BY domain " . $orderBy . " LIMIT " . $offset . "," . $totalReterive;

        $result = $db->getArray($sql);

        return $result;
    }

    //--------------------------------------------------------------------------

    public function getId()
    {
        return $this->id;
    }

    //--------------------------------------------------------------------------

    public function getEmail()
    {
        return $this->email;
    }

    //--------------------------------------------------------------------------

    public function getDomain()
    {
        return $this->domain;
    }

    //--------------------------------------------------------------------------

    public function getPassword()
    {
        return $this->password;
    }

    //--------------------------------------------------------------------------

    public function getHost()
    {
        return $this->host;
    }

    //--------------------------------------------------------------------------

    public function getUser()
    {
        return $this->user;
    }

    //--------------------------------------------------------------------------

    public function getHostPassword()
    {
        return $this->hostPassword;
    }

    //--------------------------------------------------------------------------

    public function getPort()
    {
        return $this->port;
    }

    //--------------------------------------------------------------------------
}
