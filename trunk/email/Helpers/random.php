<?php

class Random
{

    private static function getTableCount($tableName)
    {
        $db = new Database;

        $sql = "SELECT `count` FROM `counts` WHERE `query_name` = '" . $tableName . "'";

        $result = $db->getUpperLeft($sql);

        return $result;
    }
    //--------------------------------------------------------------------------


    public static function getRandomBegin($tableName, $count)
    {
        $maximum = (self::getTableCount($tableName) - $count);

        if ($maximum < 0) { $maximum = 0; }

        $range = mt_rand(0, $maximum);

        return $range;
    }
    //--------------------------------------------------------------------------


    public static function getRandomRow($count)
    {
        $range = mt_rand(0, ($count - 1));

        return $range;
    }
    //--------------------------------------------------------------------------


    public static function getRandomAdNet()
    {
        $adNet = mt_rand(1,2);

        switch ($adNet) {
            case 1 :
                return 'ADKI';
            break;

            case 2 :
                return 'OBMedia';
            break;
        }

        return false;
    }
    //--------------------------------------------------------------------------


    public static function getRandomCreativeId($creativeIds)
    {
        return $creativeIds[array_rand($creativeIds)];
    }
    //--------------------------------------------------------------------------
}