<?php

class Footer extends Database
{

    protected $id;
    protected $name;
    protected $html;
    protected $text;

    protected $tableName = 'footers';
    const      tableName = 'footers';

    public function __construct($footerId)
    {
        parent::__construct();

        $sql  = "SELECT * FROM `$this->tableName` WHERE `id` = '" . $footerId . "';";

        $result = $this->getArrayAssoc($sql);

        $this->id       = $footerId;
        $this->name     = $result['name'];
        $this->html     = $result['html'];
        $this->text     = $result['text'];
    }
    //--------------------------------------------------------------------------


    public static function getNameById($senderID)
    {
        $db = new Database;

        $sql = "SELECT `name` FROM `" . self::tableName. "` WHERE `id` = '" . $senderID . "' LIMIT 1;";
        $result = $db->getUpperLeft($sql);

        return $result;
    }
    //--------------------------------------------------------------------------


    public static function getHtmlById($senderID)
    {
        $db = new Database;

        $sql = "SELECT `html` FROM `" . self::tableName. "` WHERE `id` = '" . $senderID . "' LIMIT 1;";
        $result = $db->getUpperLeft($sql);

        return $result;
    }
    //--------------------------------------------------------------------------


    public static function getTextById($senderID)
    {
        $db = new Database;

        $sql = "SELECT `text` FROM `" . self::tableName. "` WHERE `id` = '" . $senderID . "' LIMIT 1;";
        $result = $db->getUpperLeft($sql);

        return $result;
    }
    //--------------------------------------------------------------------------


    public function getName()
    {
        return $this->name;
    }
    //--------------------------------------------------------------------------


    public function getHtml()
    {
        return $this->html;
    }
    //--------------------------------------------------------------------------


    public function getText()
    {
        return $this->text;
    }
    //--------------------------------------------------------------------------


    public function addHtml($footer, $clickSubdomain, $senderDomain, $email, $subId)
    {
        $clickUrl = 'http://' . $clickSubdomain . '.' . $senderDomain . '/email/tracking/unsubscribe.php';

        $footer = str_replace('%%clickurl%%'    , $clickUrl               , $footer);
        $footer = str_replace('%%emailhash%%'   , HTML::encodeHash($email), $footer);
        $footer = str_replace('%%subid%%'       , HTML::encodeHash($subId), $footer);
        $footer = str_replace('%%senderdomain%%', $senderDomain           , $footer);

        return $footer;
    }
    //--------------------------------------------------------------------------


    public function addText($footer, $clickSubdomain, $senderDomain, $email, $subId)
    {
        $clickUrl = 'http://' . $clickSubdomain . '.' . $senderDomain . '/email/tracking/unsubscribe.php';

        $footer = str_replace('%%clickurl%%'    , $clickUrl               , $footer);
        $footer = str_replace('%%emailhash%%'   , HTML::encodeHash($email), $footer);
        $footer = str_replace('%%subid%%'       , HTML::encodeHash($subId), $footer);
        $footer = str_replace('%%senderdomain%%', $senderDomain           , $footer);

        return $footer;
    }
    //--------------------------------------------------------------------------
}