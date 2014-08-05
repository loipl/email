<?php

class Creative extends Database
{

    protected $id;
    protected $class;
    protected $senderId;
    protected $categoryId;
    protected $name;
    protected $from;
    protected $subject;
    protected $htmlBody;
    protected $textBody;

    protected $tableName = 'creatives';
    const      tableName = 'creatives';

    public function __construct($creativeId)
    {
        parent::__construct();

        $sql  = "SELECT * FROM `$this->tableName` WHERE `id` = '" . $creativeId . "';";

        $result = $this->getArrayAssoc($sql);

        $this->id         = $creativeId;
        $this->class      = $result['class'];
        $this->senderId   = $result['sender_id'];
        $this->categoryId = $result['category_id'];
        $this->name       = $result['name'];
        $this->from       = $result['from'];
        $this->subject    = $result['subject'];
        $this->htmlBody   = $result['html_body'];
        $this->textBody   = $result['text_body'];
    }
    //--------------------------------------------------------------------------


    public static function getClassById($creativeID)
    {
        $db = new Database;

        $sql = "SELECT `class` FROM `" . self::tableName . "` WHERE `id` = '" . $creativeID . "' LIMIT 1;";
        $result = $db->getUpperLeft($sql);

        return $result;
    }
    //--------------------------------------------------------------------------


    public static function getSenderIdById($creativeID)
    {
        $db = new Database;

        $sql = "SELECT `sender_id` FROM `" . self::tableName . "` WHERE `id` = '" . $creativeID . "' LIMIT 1;";
        $result = $db->getUpperLeft($sql);

        return $result;
    }
    //--------------------------------------------------------------------------


    public static function getCategoryIdById($creativeID)
    {
        $db = new Database;

        $sql = "SELECT `category_id` FROM `" . self::tableName . "` WHERE `id` = '" . $creativeID . "' LIMIT 1;";
        $result = $db->getUpperLeft($sql);

        return $result;
    }
    //--------------------------------------------------------------------------


    public static function getNameById($creativeID)
    {
        $db = new Database;

        $sql = "SELECT `name` FROM `" . self::tableName . "` WHERE `id` = '" . $creativeID . "' LIMIT 1;";
        $result = $db->getUpperLeft($sql);

        return trim($result);
    }
    //--------------------------------------------------------------------------


    public static function getFromById($creativeID)
    {
        $db = new Database;

        $sql = "SELECT `from` FROM `" . self::tableName . "` WHERE `id` = '" . $creativeID . "' LIMIT 1;";
        $result = $db->getUpperLeft($sql);

        return trim($result);
    }
    //--------------------------------------------------------------------------


    public static function getSubjectById($creativeID)
    {
        $db = new Database;

        $sql = "SELECT `subject` FROM `" . self::tableName . "` WHERE `id` = '" . $creativeID . "' LIMIT 1;";
        $result = $db->getUpperLeft($sql);

        return trim($result);
    }
    //--------------------------------------------------------------------------


    public static function getHtmlBodyById($creativeID, $tokens = NULL)
    {
        $db = new Database;

        $sql = "SELECT `html_body` FROM `" . self::tableName . "` WHERE `id` = '" . $creativeID . "' LIMIT 1;";
        $result = $db->getUpperLeft($sql);

        if (!empty($tokens['subid'])) {
            $substitutions['subid'] = $tokens['subid'];

            $result = self::doSubstitutions($result, $substitutions);
        }

        return $result;
    }
    //--------------------------------------------------------------------------


    public static function getTextBodyById($creativeID, $tokens = NULL)
    {
        $db = new Database;

        $sql = "SELECT `text_body` FROM `" . self::tableName . "` WHERE `id` = '" . $creativeID . "' LIMIT 1;";
        $result = $db->getUpperLeft($sql);

        if (!empty($tokens['subid'])) {
            $substitutions['subid'] = $tokens['subid'];

            $result = self::doSubstitutions($result, $substitutions);
        }

        return $result;
    }
    //--------------------------------------------------------------------------


    public function getClass()
    {
        return $this->class;
    }
    //--------------------------------------------------------------------------


    public function getSenderId()
    {
        return $this->senderId;
    }
    //--------------------------------------------------------------------------


    public function getCategoryId()
    {
        return $this->categoryId;
    }
    //--------------------------------------------------------------------------


    public function getName()
    {
        return trim($this->name);
    }
    //--------------------------------------------------------------------------


    public function getFrom()
    {
        return trim($this->from);
    }
    //--------------------------------------------------------------------------


    public function getSubject()
    {
        return trim($this->subject);
    }
    //--------------------------------------------------------------------------


    public function getHtmlBody($tokens = NULL)
    {
        if (!empty($tokens['subid'])) {
            $substitutions['subid'] = $tokens['subid'];

            $htmlBody = self::doSubstitutions($this->htmlBody, $substitutions);
        } else {
            $htmlBody = $this->htmlBody;
        }

        return $htmlBody;
    }
    //--------------------------------------------------------------------------


    public function getTextBody($tokens = NULL)
    {
        if (!empty($tokens['subid'])) {
            $substitutions['subid'] = $tokens['subid'];

            $textBody = self::doSubstitutions($this->textBody, $substitutions);
        } else {
            $textBody = $this->textBody;
        }

        return $textBody;
    }
    //--------------------------------------------------------------------------


    private function doSubstitutions($text, $substitutions)
    {
        if (!empty($substitutions['subid'])) {
            $text = str_replace('[:subid:]', $substitutions['subid'], $text);
        }

        return $text;
    }
    //--------------------------------------------------------------------------
    public static function getAllCreatives()
    {
        $db = new Database;

        $sql  = "SELECT * FROM `" . self::tableName . "` LIMIT 20";

        $result = $db->getArray($sql);

        return $result;
    }
    //--------------------------------------------------------------------------
    
    public static function updateCreativeById($id, $updateData)
    {
        $db = new Database;
        
        $fields = array ('class', 'category_id', 'sender_id', 'name', 'from', 'subject', 'html_body', 'text_body');
        foreach ($updateData as $key => $value) {
            if (!in_array($key, $fields)) {
                unset($updateData[$key]);
            }
        }

        $sql  = "UPDATE `" . self::tableName . "` SET";
        
        foreach ($updateData as $field => $data) {
            if (!empty($data)) {
                $sql .= " `$field` = '" . mysql_real_escape_string($data) . "',";
            } else {
                $sql .= " `$field` = null,";
            }
        }
        $sql = rtrim($sql, ',');

        $sql .= " WHERE `id` = '" . mysql_real_escape_string($id) . "'";
        $db->query($sql);

        return true;
    }
    //--------------------------------------------------------------------------
    
    
    public static function addCreative($data)
    {
        $db = new Database;

        $sql  = 'INSERT INTO `' . self::tableName . '` (`class`, `category_id`, `sender_id`, `name`, `from`, `subject`, `html_body`, `text_body`)';
        $sql .= ' VALUES (';
        $sql .= ' \'' . mysql_real_escape_string($data['class']) . '\',';
        $sql .= ' \'' . mysql_real_escape_string($data['category_id']) . '\',';
        $sql .= ' \'' . mysql_real_escape_string($data['sender_id']) . '\',';
        $sql .= ' \'' . mysql_real_escape_string($data['name']) . '\',';
        $sql .= ' \'' . mysql_real_escape_string($data['from']) . '\',';
        $sql .= ' \'' . mysql_real_escape_string($data['subject']) . '\',';
        $sql .= ' \'' . mysql_real_escape_string($data['html_body']) . '\',';
        $sql .= ' \'' . mysql_real_escape_string($data['text_body']) . '\'';
        $sql .= ');';
        
        $db->query($sql);

        return true;
    }
    //--------------------------------------------------------------------------
}