<?php

class Dates
{

    public static function validateDate($date, $format = 'Y-m-d')
    {
        $dateCheck = DateTime::createFromFormat($format, $date);
        return $dateCheck && $dateCheck->format($format) == $date;
    }
    //--------------------------------------------------------------------------
}