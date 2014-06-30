<?php

class Category extends Database
{

    protected $id;
    protected $name;

    protected $tableName = 'categories';
    const      tableName = 'categories';

    public function __construct($categoryId)
    {
        parent::__construct();

        $sql  = "SELECT * FROM `$this->tableName` WHERE `id` = '" . $categoryId . "';";

        $result = $this->getArrayAssoc($sql);

        $this->id       = $categoryId;
        $this->name     = $result['name'];
    }
    //--------------------------------------------------------------------------


    public static function getNameById($categoryID)
    {
        $db = new Database;

        $sql = "SELECT `name` FROM `" . self::tableName. "` WHERE `id` = '" . $categoryID . "' LIMIT 1;";
        $result = $db->getUpperLeft($sql);

        return $result;
    }
    //--------------------------------------------------------------------------


    public function getName()
    {
        return $this->name;
    }
    //--------------------------------------------------------------------------
}