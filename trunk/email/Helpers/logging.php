<?php

class Logging
{


    public static function logSendError($espName, $errorCode, $errorNumber, $error, $to, $fromPerson, $fromEmail, $subject, $bodyHtml, $bodyText)
    {
        $db = new Database;

        $sql  = "INSERT INTO `error_log_esp` (id, datetime, esp_name, error_code, error_number, error, destination, from_person, from_email,";
        $sql .= " subject, body_html, body_text) VALUES (";
        $sql .= "NULL, NOW(),";
        $sql .= " '" . mysql_real_escape_string($espName) . "',";
        $sql .= " '" . mysql_real_escape_string($errorCode) . "',";
        $sql .= " '" . mysql_real_escape_string($errorNumber) . "',";
        $sql .= " '" . mysql_real_escape_string($error) . "',";
        $sql .= " '" . mysql_real_escape_string($to) . "',";
        $sql .= " '" . mysql_real_escape_string($fromPerson) . "',";
        $sql .= " '" . mysql_real_escape_string($fromEmail) . "',";
        $sql .= " '" . mysql_real_escape_string($subject) . "',";
        $sql .= " '" . mysql_real_escape_string($bodyHtml) . "',";
        $sql .= " '" . mysql_real_escape_string($bodyText) . "'";
        $sql .= ");";

        $db->query($sql);
    }
    //--------------------------------------------------------------------------


    public static function logDatabaseError($query, $error)
    {
        $db = new Database;

        $sql  = "INSERT INTO `error_log_mysql` (id, datetime, query, error) VALUES (";
        $sql .= "NULL, NOW(),";
        $sql .= " '" . mysql_real_escape_string($query) . "',";
        $sql .= " '" . mysql_real_escape_string($error) . "'";
        $sql .= ");";

        $db->query($sql);
    }
    //--------------------------------------------------------------------------


    public static function logDebugging($description, $data)
    {
        if (empty($data)) {
            return false;
        }

        $db = new Database;

        $sql  = "INSERT INTO `error_log_debug` (id, datetime, description, data) VALUES (";
        $sql .= "NULL, NOW(),";
        $sql .= " '" . mysql_real_escape_string($description) . "',";
        $sql .= " '" . mysql_real_escape_string($data) . "'";
        $sql .= ");";

        $db->query($sql);
    }
    //--------------------------------------------------------------------------


    public static function logImpressionWiseResult($result)
    {
        $db = new Database;

        $sql  = "INSERT INTO `log_impressionwise` (response) VALUES (";
        $sql .= "'" . mysql_real_escape_string($result) . "')";
        $sql .= " ON DUPLICATE KEY UPDATE `count` = `count` + 1";

        $db->query($sql);
    }
    //--------------------------------------------------------------------------


    public static function logImpressionWiseSent()
    {
        $db = new Database;

        $sql  = "INSERT INTO `log_impressionwise` (response) VALUES (";
        $sql .= "'SENT')";
        $sql .= " ON DUPLICATE KEY UPDATE `count` = `count` + 1";

        $db->query($sql);
    }
    //--------------------------------------------------------------------------
}