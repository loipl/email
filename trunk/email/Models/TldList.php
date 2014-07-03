<?php

class TldList extends Database
{

    public static $aolTldList = array(
        'aol.com',
        'aol.co.uk',
        'aim.com',
        'cs.com',
        'compuserve.com',
        'netscape.com',
        'netscape.co.uk',
        'netscape.net',
        'wmconnect.com'
    );

    public static $microsoftTldList = array(
        'hotmail.com',
        'hotmail.co.uk',
        'hotmail.ca',
        'hotmail.fr',
        'hotmail.es',
        'msn.com',
        'msn.co.uk',
        'email.msn.com',
        'live.com',
        'live.co.uk',
        'live.ca',
        'live.com.au',
        'outlook.com',
        'outlook.co.uk',
        'q.com'
    );

    public static $gmailTldList = array(
        'gmail.com',
        'googlemail.com'
    );

    public static $unitedOnlineTldList = array(
        'netzero.com',
        'netzero.net',
        'juno.com',
        'juno.co.uk'
    );

    public static $cableTldList = array(
        'comcast.net',
        'rr.com',
        'roadrunner.com',
        'centurylink.net',
        'charter.net',
        'brighthouse.com',
        'cox.net',
        'verizon.net',
        'earthlink.net',
        'excite.com',
        'optimum.net',
        'frontier.com',
        'frontiernet.net',
        'cableone.net',
        'embarqmail.com',
        'mchsi.com',
        'windstream.net',
        'hughes.net',
        'bigpond.com',
        'suddenlink.net',
        'rogers.com',
        'centurytel.net',
        'optonline.net',
        'tds.net'
    );

    public static $yahooTldList = array(
        'yahoo.com',
        'ymail.com',
        'yahoomail.com',
        'rocketmail.com',
        'yahoo.ca',
        'yahoo.co.uk',
        'yahoo.es',
        'yahoo.fr',
        'yahoo.co.id',
        'yahoo.in',
        'yahoo.co.in',
        'geocities.com',
        'ameritech.net',
        'att.net',
        'bellsouth.net',
        'btinternet.com',
        'flash.net',
        'nvbell.net',
        'pacbell.net',
        'prodigy.net',
        'sbcglobal.net',
        'snet.net',
        'swbell.net',
        'wans.net'
    );


    public static function combineTldLists() {
        return array_merge(
            TldList::$aolTldList,
            TldList::$microsoftTldList,
            TldList::$gmailTldList,
            TldList::$unitedOnlineTldList,
            TldList::$cableTldList,
            TldList::$yahooTldList
        );
    }
}